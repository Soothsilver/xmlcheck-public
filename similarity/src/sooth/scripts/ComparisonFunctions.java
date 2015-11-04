package sooth.scripts;

import sooth.Configuration;
import sooth.Logging;
import sooth.objects.Document;
import sooth.objects.Similarity;
import sooth.objects.Submission;
import sooth.similarity.*;

import java.util.logging.Logger;

/**
 * This class contains the general static function "compare" which compares two submissions for similarity and also contains
 * function that use the various algorithms to compare documents. The classes in the package sooth.similarity only
 * implement the actual algorithms and do not concern themselves with interpreting results.
 */
public class ComparisonFunctions {
    private static final Logger logger = Logging.getLogger(ComparisonFunctions.class.getName());

    /**
     * The size in bytes a document must have to be compared for similarity at all. The default is 500 (i.e. 500 bytes)
     */
    private static final int MINIMUM_DOCUMENT_LENGTH = 500;
    /**
     * Maximum size in bytes of a document in order to be comparable by the Levenshtein algorithm.
     * By default, we compare all documents because the DocumentExtractor already prevents small documents from being
     * put into the database at all.
     */
    private static final int LEVENSHTEIN_MAXIMUM_DOCUMENT_SIZE = Integer.MAX_VALUE;
    /**
     * Maximum size in bytes of a document in order to be comparable by the Greedy-String-Tiling algorithm.
     */
    private static final int GREEDY_MAXIMUM_DOCUMENT_SIZE = 2000;
    /**
     * If the difference in bytes of two documents is greater than this, then it is highly unlikely the documents will
     * be considered similar and thus are not compared by Levenshtein algorithm at all.
     */
    private static final int OBVIOUS_SIZE_DIFFERENCE = 2000;
    /**
     * The singleton instance of the Greedy-String-Tiling algorithm. Because this algorithm does not keep any local
     * variables across runs, it is thread-safe and thus can be here as a singleton.
     */
    private static final GreedyStringTilingAlgorithm greedyStringTilingAlgorithm = new GreedyStringTilingAlgorithm();

    /**
     * Compares two documents using Zhang-Shasha algorithm. This method is thread-safe. Both documents MUST have correctly formed trees or this function will throw an exception.
     * @param oldDocument The first document.
     * @param newDocument The second document.
     * @return An object describing the similarity of the documents.
     */
    private static DocumentComparisonResult zhangShashaCompare(Document oldDocument, Document newDocument)
    {
        Document.DocumentType type = oldDocument.getType();
        ZhangShashaTree oldDocumentTree = oldDocument.getZhangShashaTree();
        ZhangShashaTree newDocumentTree = newDocument.getZhangShashaTree();

        switch (type) {
            // These are not tested because they are too small to test meaningfully.
            // case XQUERY_ADDITIONAL_XML_FILE:
            // case XQUERY_QUERY:
            // case XPATH_QUERY:

            // These are not tested because they are not XML files:
            // case DTD_FILE:
            // case JAVA_DOM_TRANSFORMER:
            // case JAVA_SAX_HANDLER:

            // Traditional XML files:
            case PRIMARY_XML_FILE:
            case XSLT_SCRIPT:
            case XSD_SCHEMA:
                // Use defaults.
                break;
            default:
                return new DocumentComparisonResult(0, "Documents of this type are not compared automatically by the Zhang-Shasha comparison algorithm.", false);
        }

        int distance = ZhangShashaAlgorithm.getInstance().compare(oldDocumentTree, newDocumentTree);
        int score = 100 - ((100 * distance) / ((oldDocumentTree.getNodeCount() * ZhangShashaAlgorithm.DELETION_COST) + (newDocumentTree.getNodeCount() * ZhangShashaAlgorithm.INSERTION_COST)));

        return new DocumentComparisonResult(score,
                "The tree edit distance is " + distance + ". The first tree has " + oldDocumentTree.getNodeCount() + " nodes. The second tree has " + newDocumentTree.getNodeCount() + ".",
                score >= Configuration.zhangShashaSuspicionThreshold);

    }

    /**
     * Compares two documents using the Greedy-String-Tiling algorithm. This method is thread-safe.
     * @param oldDocument The first document.
     * @param newDocument The second document.
     * @return An object describing the similarity of the documents.
     */
    public static DocumentComparisonResult greedyStringTilingCompare(Document oldDocument, Document newDocument)
    {
        Document.DocumentType type = oldDocument.getType();
        String oldDocumentText = oldDocument.getPreprocessedText();
        String newDocumentText = newDocument.getPreprocessedText();
        float weight = 1;

        switch (type) {

            // These are not tested because they are too small to test meaningfully.
            // case DTD_FILE:
            // case XQUERY_ADDITIONAL_XML_FILE:
            // case XQUERY_QUERY:
            // case XPATH_QUERY:

            // Traditional XML files:
            case PRIMARY_XML_FILE:
            case XSLT_SCRIPT:
            case XSD_SCHEMA:
                // Java scripts:
            case JAVA_DOM_TRANSFORMER:
            case JAVA_SAX_HANDLER:
                // Use defaults.
                break;
            default:
                return new DocumentComparisonResult(0, "Documents of this type are not compared automatically by the Greedy-String-Tiling algorithm.", false);
        }

        // Obvious size mismatch
        if (Math.abs(oldDocumentText.length() - newDocumentText.length()) > OBVIOUS_SIZE_DIFFERENCE)
        {
            return new DocumentComparisonResult(0, "One document has at least " + OBVIOUS_SIZE_DIFFERENCE + " more characters than the other one. It is unlikely they were copied from each other. Skipping the entire plagiarism check.", false);
        }
        int similarity = 0;

        // Greedy-String-Tiling comparison, if the document is small enough
        if ((oldDocumentText.length() < GREEDY_MAXIMUM_DOCUMENT_SIZE) && (newDocumentText.length() < GREEDY_MAXIMUM_DOCUMENT_SIZE)) {
            int matchedCharacters = greedyStringTilingAlgorithm.compare(oldDocumentText, newDocumentText);

            int score = (100 * 2 * matchedCharacters) / (oldDocumentText.length() + newDocumentText.length());

            return new DocumentComparisonResult((int)(score * weight), "The number of matched character is " + matchedCharacters + ". The document sizes are " + oldDocumentText.length() + " and " + newDocumentText.length() + ".",
                    false);

        } else {
            similarity = Math.max(similarity, 0);
            return new DocumentComparisonResult(similarity, "The Greedy-String-Tiling test was not performed because the size of one of the documents is too large.", false);
        }
    }

    /**
     * Compares two documents using the Levenshtein distance algorithm. This method is thread-safe.
     * @param oldDocument The first document.
     * @param newDocument The second document.
     * @return An object describing the similarity of the documents.
     */
    private static DocumentComparisonResult levenshteinCompare(Document oldDocument, Document newDocument)
    {
        Document.DocumentType type = oldDocument.getType();
        String oldDocumentText = oldDocument.getPreprocessedText();
        String newDocumentText = newDocument.getPreprocessedText();
        float weight = 1;

        switch (type) {
                // These are not tested because they are too small to test meaningfully.
                // case XQUERY_ADDITIONAL_XML_FILE:
                // case XQUERY_QUERY:
                // case XPATH_QUERY:
                // case DTD_FILE:

                // Traditional XML files:
            case PRIMARY_XML_FILE:
            case XSLT_SCRIPT:
            case XSD_SCHEMA:
                // Java scripts:
            case JAVA_DOM_TRANSFORMER:
            case JAVA_SAX_HANDLER:
                // Use defaults.
                break;
            default:
                return new DocumentComparisonResult(0, "Documents of this type are not compared automatically by Levenshtein distance.", false);
        }

                // Obvious size mismatch
                if (Math.abs(oldDocumentText.length() - newDocumentText.length()) > OBVIOUS_SIZE_DIFFERENCE)
                {
                    return new DocumentComparisonResult(0, "One document has at least " + OBVIOUS_SIZE_DIFFERENCE + " more characters than the other one. It is unlikely they were copied from each other. Skipping the entire plagiarism check.", false);
                }
                int similarity = 0;

                // Levenshtein comparison, if the document is small enough
                if ((oldDocumentText.length() < LEVENSHTEIN_MAXIMUM_DOCUMENT_SIZE) && (newDocumentText.length() < LEVENSHTEIN_MAXIMUM_DOCUMENT_SIZE)) {
                    int distance = LevenshteinDistanceAlgorithm.getInstance().compare(oldDocumentText, newDocumentText);

                    int score = 100 - ((100 * distance) / (Math.max(oldDocumentText.length(), newDocumentText.length())));

                    return new DocumentComparisonResult((int)(score * weight),
                            "The Levenshtein distance of the documents is " + distance + ". The bigger document length is " + (Math.max(oldDocumentText.length(), newDocumentText.length())) + ".",
                            score >= Configuration.levenshteinSuspicionThreshold);

                } else {
                    similarity = Math.max(similarity, 0);
                    return new DocumentComparisonResult(similarity,
                            "The Levenshtein test was not performed because the size of one of the documents is too large.",
                            false);
                }
}

    /**
     * Compares the two submissions for similarity and returns a similarity record ready to be inserted into the database. This function is thread-safe.
     * The function currently uses Zhang-Shasha algorithm and if that does detect suspicious similarity, then Levenshtein algorithm is used.
     * The used algorithm can be changed inside the code of this method. If you simply want to not use Zhang-Shasha, you may just set
     * "enableZhangShasha" in the config.ini file to false.
     * @param oldSubmission The first submission.
     * @param newSubmission The second submission.
     * @return A similarity record comparing the two submissions.
     */
    public static Similarity compare(Submission oldSubmission, Submission newSubmission) {
        // Compare all documents to all documents.
        // Default metric: take the highest similarity from among documents
        Similarity similarity = new Similarity(0, "", oldSubmission.getSubmissionId(), newSubmission.getSubmissionId(), false);
        for (Document oldDocument : oldSubmission.getDocuments())
        {

            for (Document newDocument : newSubmission.getDocuments())
            {

                if (oldDocument.getType().equals(newDocument.getType()))
                {
                    if ((oldDocument.getPreprocessedText().length() < MINIMUM_DOCUMENT_LENGTH) ||
                            (newDocument.getPreprocessedText().length() < MINIMUM_DOCUMENT_LENGTH))
                    {
                        // It is meaningless to compare documents this small for similarity because they will be trivial.
                        continue;
                    }
                    logger.fine("Now comparing " + oldDocument.getType() + " documents.");


                    // Zhang-Shasha

                    DocumentComparisonResult result = null;
                    if ((oldDocument.getType() == Document.DocumentType.PRIMARY_XML_FILE) ||
                            (oldDocument.getType() == Document.DocumentType.XSD_SCHEMA) ||
                            (oldDocument.getType() == Document.DocumentType.XSLT_SCRIPT)) {
                        if ((oldDocument.getZhangShashaTree() != null) && (newDocument.getZhangShashaTree() != null)) {
                            result = zhangShashaCompare(oldDocument, newDocument);
                            if (!result.isSuspicious()) {
                                result = null; // We will still try a Levenshtein comparison if Zhang-Shasha did not trigger.
                            }
                        }
                    }

                    if (result == null) {
                        result = levenshteinCompare(oldDocument, newDocument);
                    }

                    if (similarity.getScore() < result.getSimilarity()) {
                        similarity.setScore(result.getSimilarity());
                    }
                    if (result.isSuspicious()) {
                        similarity.setSuspicious(true);
                    }
                    if (result.isSuspicious() || (result.getSimilarity() >= Configuration.levenshteinMasterThreshold))
                    {
                        similarity.setDetails(similarity.getDetails() +
                                oldDocument.getType() + " comparison (" + result.getSimilarity() + "%"+(result.isSuspicious() ? ", suspicious" : "")+ "):\n"
                                + "Details: \n" + result.getDetails() + "\n\n");
                    }
                }
            }
        }
        if (similarity.getDetails().equals("")) {
            similarity.setDetails("No similarity detected.");
        }
        return similarity;
    }
}
