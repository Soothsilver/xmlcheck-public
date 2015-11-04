package sooth.similarity;

/**
 * Represents the result of a similarity comparison of two documents.
 *
 * This is different from the @ref Similarity class, because that class represents the comparison result of two submissions.
 * (A submission contains multiple documents.)
 */
public class DocumentComparisonResult {
    /**
     * A value, from 0 to 100, that indicates the measure of similarity of the two submissions. A value of 100 means
     * the submissions are identical.
     */
    private final int similarity;
    /**
     * Additional information about the executed comparison test.
     */
    private final String details;
    /**
     * A value indicating whether there is an extremely high probability that one of the two submissions is plagiarism.
     * If any document comparison is flagged as suspicious, the higher-level submission comparison will also be flagged
     * suspicious.
     */
    private final boolean suspicious;

    /**
     * Gets a value indicating whether there is an extremely high probability that one of the two submissions is plagiarism.
     * @return A value indicating whether there is an extremely high probability that one of the two submissions is plagiarism.
     */
    public boolean isSuspicious() {
        return suspicious;
    }

    /**
     * Initializes a new instance of the DocumentComparisonResult class that represents the result of a similarity comparison of two submissions.
     * @param similarity A value, from 0 to 100, that indicates the measure of similarity of the two submissions. A value of 100 means the submissions are identical.
     * @param details Additional information about the executed comparison test.
     * @param suspicious A value indicating whether there is an extremely high probability that one of the two submissions is plagiarism.
     */
    public DocumentComparisonResult(int similarity, String details, boolean suspicious) {
        this.similarity = similarity;
        this.details = details;
        this.suspicious = suspicious;
    }

    /**
     * Gets a value, from 0 to 100, that indicates the measure of similarity of the two submissions. A value of 100 means
     * the submissions are identical.
     * @return A value, from 0 to 100, that indicates the measure of similarity of the two submissions. A value of 100 means the submissions are identical.
     */
    public int getSimilarity() {
        return similarity;
    }

    /**
     * Gets additional information about the executed comparison test.
     * @return Additional information about the executed comparison test.
     */
    public String getDetails() {
        return details;
    }
}
