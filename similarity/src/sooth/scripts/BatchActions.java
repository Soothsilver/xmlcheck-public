package sooth.scripts;

import org.jooq.DSLContext;
import org.jooq.Result;

import sooth.Logging;
import sooth.connection.Database;
import sooth.connection.InsertSimilaritiesBatch;
import sooth.entities.Tables;
import sooth.entities.tables.records.SubmissionsRecord;
import sooth.objects.Similarity;
import sooth.objects.Submission;
import sooth.objects.SubmissionsByPlugin;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.RandomAccessFile;
import java.nio.channels.FileLock;
import java.util.ArrayList;
import java.util.Date;
import java.util.SortedSet;
import java.util.TreeSet;
import java.util.logging.Logger;

/**
 * This class contains static functions that perform actions on the entire database.
 */
public class BatchActions {
    private static final Logger logger = Logging.getLogger(BatchActions.class.getName());
    /**
     * Deletes all documents and similarities detected, recreates all documents, and then runs similarity checking on everything.
     * This method will take a great amount of time to run.
     */
    public static void makeEntireDatabase() {
        BatchActions.createDocumentsFromAllSubmissions();
        BatchActions.runPlagiarismCheckingOnEntireDatabase();
    }

    /**
     * Deletes all similarity records from the database, then runs similarity checking on everything.
     */
    public static void runPlagiarismCheckingOnEntireDatabase() {
        logger.info("Time: " + new Date());
        BatchActions.destroyAllSimilarities();
        logger.info("I will now recheck the entire database for plagiarism.");
        logger.info("Extracting documents and submissions from database to memory.");
        SubmissionsByPlugin submissionsByPlugin = Database.runSubmissionsByPluginQueryOnAllIdentifiers();
        logger.info("Creating comparison commands.");

        int totalComparisons = 100; // safety margin
        for (ArrayList<Submission> submissions : submissionsByPlugin.values()) {
            totalComparisons += (submissions.size() * (submissions.size() + 1)) / 2;
        }

        SimilarityCheckingBatch similarityBatch = new SimilarityCheckingBatch(totalComparisons);
        for (ArrayList<Submission> submissions : submissionsByPlugin.values())
        {
            if (submissions.isEmpty()) {
                continue;
            }
            logger.info("Identifier category: " + submissions.get(0).getPluginIdentifier() + " (count " + submissions.size() + ")");
            logger.info("Time: " + new Date());

            // For debugging. Enable this if you only want to check submissions for a specific plugin.
            /*
            if (!Objects.equals(submissions.get(0).getPluginIdentifier(), Problems.HW1_DTD)) {
                logger.info("Ignoring.");
                continue;
            }
            */

            for (int i = 1; i < submissions.size(); i++) {
                similarityBatch.addComparisonOfOneToMany(submissions.get(i), submissions, 0, i);
            }

        }

        logger.info("There are "  + similarityBatch.size() + " similarity commands.");
        logger.info("Executing them!");
        logger.info("Time: " + new Date());
        Iterable<Similarity> similarities = similarityBatch.execute();
        logger.info("Submitting them to the database!");
        logger.info("Time: " + new Date());
        InsertSimilaritiesBatch batch = new InsertSimilaritiesBatch();
        for (Similarity similarity : similarities) {
            if (similarity.getScore() >= Similarity.MINIMUM_INTERESTING_SCORE) {
                batch.add(similarity);
            }
        }
        batch.execute();
        logger.info("Done.");
        logger.info("Time: " + new Date());
        logger.info("The entire database has been fully checked for plagiarism.");
    }

    /**
     * Destroys all document records in the database, then reloads documents from disk (from ZIP files) into the documents table in the database.
     * This method will take a lot of time because it makes a lot of I/O operations.
     */
    public static void createDocumentsFromAllSubmissions() {
        logger.info("Time: " + new Date());
        BatchActions.destroyAllDocuments();
        logger.info("I will now recreate document records from all submissions.");
        DSLContext context = Database.getContext();
        Result<SubmissionsRecord> submissions = context.selectFrom(Tables.SUBMISSIONS).where(Tables.SUBMISSIONS.STATUS.notEqual("deleted")).fetch();
        submissions.forEach(DocumentExtractor::createDatabaseDocumentsFromSubmissionRecord);
        logger.info("Document records created.");
        logger.info("Time: " + new Date());
    }

    /**
     * Removes all similarity records from the database by truncating the similarities table.
     */
    private static void destroyAllSimilarities() {
        logger.info("I will destroy all similarities.");
        DSLContext context = Database.getContext();
        context.truncate(Tables.SIMILARITIES).execute();
        context.delete(Tables.SIMILARITIES).execute();
        logger.info("All similarities destroyed and removed from the database.");
    }

    /**
     * Removes all document records from the database by truncating the documents table.
     */
    private static void destroyAllDocuments() {
        logger.info("I will destroy all documents.");
        DSLContext context = Database.getContext();
        context.truncate(Tables.DOCUMENTS).execute();
        context.delete(Tables.DOCUMENTS).execute();
        logger.info("All documents destroyed and removed from the database..");
    }

    /**
     * Discovers if another instance of this module is already running.
     * If it is, this function does nothing.
     * If it's not, then new submissions that have yet to be analyzed for similarity, are analyzed and then the process is repeated until there are no new submissions in the database.
     */
    public static void extractAndAnalyzeNewSubmissionsIfPossible() {
        // First, make a lock.
        File lockFile = new File("similarity.lock");
        RandomAccessFile randomAccessFile;
        try {
            randomAccessFile = new RandomAccessFile(lockFile, "rw");
        } catch (FileNotFoundException ignored) {
            System.err.println("You don't have privileges to open a lock file.");
            return;
        }
        try {
            FileLock fileLock = randomAccessFile.getChannel().tryLock();
            if (fileLock == null) {
                System.out.println("Another instance of the similarity module is in progress. Aborting this instance.");
                return;
            }

            // We are now the only instance running.
            //noinspection StatementWithEmptyBody
            while (extractAndAnalyzeNewSubmissions()) {
                // Repeat until false is returned.
            }
        } catch (IOException e) {
            System.err.println("An error occurred when attempting to secure a file lock.");
        }
    }

    /**
     * Retrieves from the database all submissions that are yet to be analyzed for similarity, loads the contents of their files into the database and analyzes them.
     * @return Returns true if any submissions were analyzed. Returns false if there are no new submissions in the database.
     */
    private static boolean extractAndAnalyzeNewSubmissions() {
        // We are now the only instance running.

        // Find new submissions.
        logger.info("Time: " + new Date());
        DSLContext context = Database.getContext();
        Result<SubmissionsRecord> submissions =
                context.selectFrom(Tables.SUBMISSIONS)
                       .where(Tables.SUBMISSIONS.STATUS.notEqual("deleted"))
                       .and(Tables.SUBMISSIONS.SIMILARITYSTATUS.equal("new")).fetch();

        if (submissions.isEmpty()) {
            logger.info("No new submission detected. Success.");
            return false;
        }
        // Create documents
        logger.info("Time: " + new Date());
        logger.info("I will now recreate document records from all new submissions.");
        submissions.forEach(DocumentExtractor::createDatabaseDocumentsFromSubmissionRecord);
        logger.info("Time: " + new Date());
        logger.info("Document records created.");

        // Create set of submission ids that need to be checked
        SortedSet<Integer> newSubmissions = new TreeSet<>();
        //noinspection Convert2streamapi
        for (SubmissionsRecord record : submissions) {
            newSubmissions.add(record.getId());
        }

        SubmissionsByPlugin submissionsByPlugin = Database.runSubmissionsByPluginQueryOnAllIdentifiers();

        // Set initial capacity
        int totalComparisons = 100;
        for (ArrayList<Submission> typedSubmissions : submissionsByPlugin.values()) {
            totalComparisons += (typedSubmissions.size() * (typedSubmissions.size() + 1)) / 2;
        }

        // Create comparison commands
        SimilarityCheckingBatch similarityBatch = new SimilarityCheckingBatch(totalComparisons);
        for (ArrayList<Submission> typedSubmissions : submissionsByPlugin.values())
        {
            if (submissions.isEmpty()) {
                continue;
            }

            for (int i = 1; i < typedSubmissions.size(); i++) {
                if (newSubmissions.contains(new Integer(typedSubmissions.get(i).getSubmissionId()))) {
                    similarityBatch.addComparisonOfOneToMany(typedSubmissions.get(i), typedSubmissions, 0, i);
                }
            }
        }

        logger.info("There are "  + similarityBatch.size() + " similarity commands. Executing.");
        Iterable<Similarity> similarities = similarityBatch.execute();
        InsertSimilaritiesBatch batch = new InsertSimilaritiesBatch();
        for (Similarity similarity : similarities) {
                batch.add(similarity);
        }
        batch.execute();
        logger.info("Time: " + new Date());
        logger.info("Updating status to 'checked'.");

        // Send to database information that these submissions are checked
        for (SubmissionsRecord record : submissions) {
            record.setSimilaritystatus("checked");
        }
        context.batchStore(submissions).execute();
        logger.info("Time: " + new Date() + ". Done.");

        // Determine guilt or innocence
        logger.info("Determining guilt...");
        redetermineGuiltOrInnocence();
        logger.info("Time: " + new Date() + ". Done.");

        // Repeat?
        logger.info("At least one new submission was processed. Similarity checking will not be immediately repeated.");
        return true;
    }

    /**
     * Runs two MySQL queries that update the similarity status of all submissions in the database thus:
     * 1. All submissions checked by this module that are suspiciously similar to at least one other submission are considered 'guilty' (i.e. plagiarisms)
     * 2. All other submissions checked by this module are considered 'innocent'.
     */
    private static void redetermineGuiltOrInnocence() {
        DSLContext context = Database.getContext();
        String guiltyQuery =
                "UPDATE submissions SET submissions.similarityStatus = 'guilty' " +
                "WHERE submissions.similarityStatus = 'checked' " +
                "AND submissions.status <> 'deleted' " +
                "AND EXISTS ( SELECT id FROM similarities WHERE similarities.newSubmissionId = submissions.id AND similarities.suspicious = 1)";
        context.execute(guiltyQuery);
        String innocentQuery =
                "UPDATE submissions SET submissions.similarityStatus = 'innocent' " +
                        "WHERE submissions.similarityStatus = 'checked' " +
                        "AND submissions.status <> 'deleted' " +
                        "AND NOT EXISTS ( SELECT id FROM similarities WHERE similarities.newSubmissionId = submissions.id AND similarities.suspicious = 1)";
        context.execute(innocentQuery);
    }
}
