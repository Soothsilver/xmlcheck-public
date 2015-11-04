package sooth;

import org.ini4j.Wini;
import org.jooq.DSLContext;
import sooth.connection.Database;
import sooth.entities.Tables;
import sooth.entities.tables.records.SubmissionsRecord;
import sooth.objects.Similarity;
import sooth.objects.Submission;
import sooth.objects.SubmissionsByPlugin;
import sooth.scripts.BatchActions;
import sooth.scripts.ComparisonFunctions;
import sooth.scripts.DocumentExtractor;

import java.io.File;
import java.io.IOException;
import java.util.ArrayList;
import java.util.Map;

/**
 * This class is the entry point for the similarity.jar archive. Its main method is called from the main application.
 */
public class EntryPoint {
    private static final String ACTION_HELP = "help";
    private static final String ACTION_RELOAD_ALL_DOCUMENTS = "reloadalldocuments";
    private static final String ACTION_RECHECK_ENTIRE_DATABASE = "recheckall";
    private static final String ACTION_EXTRACT_DOCUMENTS_FROM_ONE = "makeone";
    private static final String ACTION_MAKE_ALL = "makeall";
    private static final String ACTION_COMPARE_TWO_DIRECTLY = "compare";
    private static final String ACTION_EXTRACT_AND_ANALYZE_NEW_SUBMISSIONS_IF_POSSIBLE = "comparenew";

    private static final String ERROR_NOT_ENOUGH_ARGUMENTS = "You did not supply enough arguments for this action.";
    private static final String ERROR_MUST_BE_INTEGER = "The argument must be an integer.";
    private static final String ERROR_SUBMISSION_DOES_NOT_EXIST = "The specified submission does not exist.";

    /**
     * This method is called when the main application causes similarity.jar to run.
     * @param args Command-line arguments.
     */
    public static void main(String[] args) {

        // Load configuration
        try {
            Configuration.loadFromConfigIni(new Wini(new File("config.ini")));
        } catch (IOException e) {
            System.err.println("The config.ini file must be located in the current working directory.");
            return;
        }

        if (!Configuration.enableSimilarityChecking) {
            System.out.println("Similarity checking is disabled in the configuration file.");
            System.out.println("Put enableSimilarityChecking = true in the file to proceed.");
            System.exit(1);
            return;
        }

        // Based on the first argument, do something.
        if (args.length == 0) {
            printHelp();
            return;
        }
        String action = args[0];
        switch (action.toLowerCase()) {
            case ACTION_HELP:
                printHelp();
                return;
            case ACTION_RELOAD_ALL_DOCUMENTS:
                BatchActions.createDocumentsFromAllSubmissions();
                return;
            case ACTION_RECHECK_ENTIRE_DATABASE:
                BatchActions.runPlagiarismCheckingOnEntireDatabase();
                return;
            case ACTION_MAKE_ALL:
                BatchActions.makeEntireDatabase();
                return;
            case ACTION_EXTRACT_DOCUMENTS_FROM_ONE:
                if (args.length < 2) {
                    System.err.println(ERROR_NOT_ENOUGH_ARGUMENTS);
                    System.exit(1);
                    return;
                }
                int argumentId;
                try {
                    argumentId = Integer.parseInt(args[1]);
                }
                catch (NumberFormatException exception) {
                    System.err.println(ERROR_MUST_BE_INTEGER);
                    System.exit(1);
                    return;
                }
                DSLContext context = Database.getContext();
                SubmissionsRecord thisSubmission = context.selectFrom(Tables.SUBMISSIONS).where(Tables.SUBMISSIONS.ID.equal(argumentId)).fetchOne();
                if (thisSubmission == null) {
                    System.err.println(ERROR_SUBMISSION_DOES_NOT_EXIST);
                    System.exit(1);
                    return;
                }
                DocumentExtractor.createDatabaseDocumentsFromSubmissionRecord(thisSubmission);
                return;
            case ACTION_COMPARE_TWO_DIRECTLY:
                int submissionOne;
                int submissionTwo;
                try {
                    submissionOne = Integer.parseInt(args[1]);
                    submissionTwo = Integer.parseInt(args[2]);
                }
                catch (NumberFormatException exception) {
                    System.out.println(ERROR_MUST_BE_INTEGER);
                    System.exit(1);
                    return;
                }

                SubmissionsByPlugin submissionsByPlugin = Database.runSubmissionsByPluginQueryOnTheseSubmissions(submissionOne, submissionTwo);
                if (submissionsByPlugin.isEmpty()) {
                    System.err.println("These submissions do not exist.");
                    return;
                } else if (submissionsByPlugin.size() > 1) {
                    System.err.println("These submissions do not share the corrective plugin.");
                    return;
                }
                Map.Entry<String, ArrayList<Submission>> entry = submissionsByPlugin.entrySet().iterator().next();
                ArrayList<Submission> submissions = entry.getValue();
                if (submissions.size() != 2) {
                    System.err.println("Only one of the two submissions was found.");
                    return;
                }
                Similarity similarity = ComparisonFunctions.compare(submissions.get(0), submissions.get(1));
                System.out.println("Score: " + similarity.getScore());
                System.out.println(similarity.getDetails());
                return;
            case ACTION_EXTRACT_AND_ANALYZE_NEW_SUBMISSIONS_IF_POSSIBLE:
                BatchActions.extractAndAnalyzeNewSubmissionsIfPossible();
                return;
            default:
                System.out.println("Argument 1 (action) not recognized.");
                printHelp();
                System.exit(1);
        }
    }

    /**
     * Prints usage information to the standard output.
     */
    private static void printHelp() {
        String help;
        help = "sooth.similarity: XML Check module checking for plagiarism\n";
        help += "This program must be located inside the core/ folder of the system.\n";
        help += "Usage: java -jar similarity.jar [action] [arguments, if any]\n\n";
        help += "Actions: \n";
        help += ACTION_HELP + ": Print this message.\n";
        help += ACTION_RELOAD_ALL_DOCUMENTS + ": Delete all documents from database and reload them anew from files.\n";
        help += ACTION_RECHECK_ENTIRE_DATABASE + ": Delete all similarity records from database and recalculate them anew from documents in the database.\n";
        help += ACTION_MAKE_ALL + ": Reload all documents, then recheck entire database (the two actions above).\n";
        help += ACTION_COMPARE_TWO_DIRECTLY + " [id1] [id2]: Run similarity checking on the two specified submissions in the database.\n";
        help += ACTION_EXTRACT_DOCUMENTS_FROM_ONE + " [id1]: Extract documents from the submission with specified ID.\n";
        help += ACTION_EXTRACT_AND_ANALYZE_NEW_SUBMISSIONS_IF_POSSIBLE + ": Load new submissions from the database, extract documents and return them to database, and run similarity checking on them. This only happens if this module is not already running.\n";
        System.out.println(help);
    }
}
