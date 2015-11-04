package sooth.scripts;

import sooth.Logging;
import sooth.Configuration;
import sooth.connection.InsertSimilaritiesBatch;
import sooth.objects.Similarity;
import sooth.objects.Submission;

import java.util.ArrayList;
import java.util.Date;
import java.util.List;
import java.util.logging.Logger;

/**
 * This class gets a list of similarity commands, i.e. requests for comparisons, performs them and either returns
 * the resultant similarity records or puts them directly into the database. See functions addComparisonOfOneToMany
 * and execute.
 */
public class SimilarityCheckingBatch {
    /**
     * If true, the module will use all available processor cores. If false, only one processor core will be used.
     */
    private static final boolean useMultithreading = true;

    private final Logger logger = Logging.getLogger(SimilarityCheckingBatch.class.getName());

    /**
     * This data structure represents a request to compare two submissions.
     */
    private class SimilarityCommand {
        private final Submission oldSubmission;
        private final Submission newSubmission;

        /**
         * Initializes a new instance of the SimilarityCommand class which represents a request to compare two submissions.
         * @param oldSubmission The older submission.
         * @param newSubmission The newer submission.
         */
        public SimilarityCommand(Submission oldSubmission, Submission newSubmission) {
            this.oldSubmission = oldSubmission;
            this.newSubmission = newSubmission;
        }
    }

    private final ArrayList<SimilarityCommand> commands;

    /**
     * Initializes a new instance of the SimilarityCheckingBatch class.
     * @param capacity The expected number of commands to be executed using this batch.
     */
    public SimilarityCheckingBatch(int capacity) {
        commands = new ArrayList<>(capacity);
    }

    /**
     * Adds to the batch a number of similarity commands. The similarity commands added are the requests for comparison
     * between all submissions between the two specified integers (as the older submission) and the single newer submission.
     * @param newSubmission This submission will be compared to all the other (older) ones.
     * @param submissions The list of all older submissions.
     * @param from Comparisons will begin at this index of the list.
     * @param upToExclusive This is the index of the first submission from the list that will be excluded from the comparisons.
     */
    public void addComparisonOfOneToMany(Submission newSubmission, ArrayList<Submission> submissions, int from, int upToExclusive) {
        for (int i = from; i < upToExclusive; i++) {
            if (submissions.get(i).getUserId() == newSubmission.getUserId())
            {
                // These submissions were uploaded by the same user.
                if (Configuration.ignoringSelfPlagiarism())
                {
                    continue;
                }
            }
            commands.add(new SimilarityCommand(submissions.get(i), newSubmission));
        }
    }

    /**
     * Gets the number of similarity commands in the batch.
     * @return The number of similarity commands in the batch.
     */
    public int size() {
        return commands.size();
    }

    /**
     * In multiple threads, executes all commands in the batch.
     * This function will attempt to return the resultant similarity records as a return value but if there is too
     * many of them, then some of them will be inserted directly into the database instead of returned.
     * @return The similarity records that were not inserted into the database.
     */
    public Iterable<Similarity> execute() {
        int coreCount = Runtime.getRuntime().availableProcessors();
        //noinspection ConstantConditions
        if (!useMultithreading) {
            coreCount = 1;
        }
        int size = commands.size();
        int workload = size / coreCount;
        int at = 0;
        logger.info("Forking...");
        SimilarityInsertionQueue queue = new SimilarityInsertionQueue();
        ArrayList<Thread> threads = new ArrayList<>();
        logger.info("Queue made.");
        for (int threadIndex = 0; threadIndex < (coreCount - 1); threadIndex++) {
            Runner runner = new Runner(threadIndex+1, commands, at, at+ workload, queue);
            Thread thread = new Thread(runner);
            thread.start();
            threads.add(thread);
            logger.info("Thread " + (threadIndex+1) + " started.");
            at += workload;
        }
        logger.info("Main thread started.");
        Runner runnerFinal = new Runner(coreCount, commands, at, size, queue);
        runnerFinal.run();

        logger.info("Collecting threads...");
        logger.info("Main thread ended.");
        for(Thread t : threads) {
            try {
                t.join();
            } catch (InterruptedException ignored) {
                // Won't happen.
            }
            logger.info("A thread joined.");
        }

        logger.info("Executed.");
        return queue;
    }

    /**
     * This is a thread-safe queue of similarities. When it is full, it adds these similarities to the database using
     * a jOOQ query.
     */
    private class SimilarityInsertionQueue extends java.util.ArrayList<Similarity> {
        private int count = 0;

        /**
         * Initializes a new instance of this thread-safe queue, with capacity equal to the batch size of InsertSimilaritiesBatch.
         */
        public SimilarityInsertionQueue() {
            super(InsertSimilaritiesBatch.batchSize);
        }

        @Override
        public synchronized boolean add(Similarity similarity) {
            count++;
            if (count == InsertSimilaritiesBatch.batchSize) {
                InsertSimilaritiesBatch batch = new InsertSimilaritiesBatch();
                for (int i = 0; i < (count - 1); i++) {
                    batch.add(this.get(i));
                }
                count = 1;
                batch.execute();
                logger.info("Batch executed.");
                this.clear();
            }
            return super.add(similarity);
        }
    }

    /**
     * This class represents a single worker thread that performs similarity comparisons.
     */
    private class Runner implements Runnable {
        private final List<SimilarityCommand> commands;
        private final int from;
        private final int upToExclusive;
        private final SimilarityInsertionQueue queue;
        private final int threadIndex;

        /**
         * Initializes a new instance of the Runner class.
         * @param threadIndex Integer identifier of this thread (used for logging purposes only).
         * @param commands A list of comparisons to be executed.
         * @param from From the list, only perform comparisons with index greater or equal to this value.
         * @param upToExclusive From the list, only perform comparisons with index lesser than this value.
         * @param queue A thread-safe queue where found similarities are inserted.
         */
        public Runner(int threadIndex, List<SimilarityCommand> commands, int from, int upToExclusive, SimilarityInsertionQueue queue) {
            this.threadIndex = threadIndex;
            this.commands = commands;
            this.from = from;
            this.upToExclusive = upToExclusive;
            this.queue = queue;
        }

        @Override
        public void run() {
            int count = upToExclusive - from;
            int k = 1;
            for (int i = from; i < upToExclusive; i++) {
                Similarity similarity = ComparisonFunctions.compare(commands.get(i).oldSubmission, commands.get(i).newSubmission);
                if (similarity.isSuspicious() || (similarity.getScore() >= Configuration.levenshteinMasterThreshold)) {
                    queue.add(similarity);
                }
                if (i == (from + ((count * k) / 10)))
                {
                    System.gc();
                    logger.info("Thread " +  threadIndex + " - Percent done: " + (k*10 + " in time " + new Date()));
                    k++;
                }
            }
        }
    }
}
