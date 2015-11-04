package sooth.objects;

/**
 * Represents the results of a similarity check between two submissions.
 *
 * This is different from the DocumentComparisonResult, because that class represents the similarity of two documents.
 * (A submission contains multiple documents.)
 *
 * This class maps to a row of the similarities table in the database.
 *
 * It is important to distinguish between the older and the new submission. A comparison always compares two submissions
 * of different submission date. The "old submission" may never be newer than the "new submission". In the user interface,
 * this similarity result is displayed only when looking at the details of the newer submission.
 */
public class Similarity {
    /**
     * Similarities with score lesser than this value are definitely not plagiarisms and therefore don't need to be
     * inserted into the database.
     */
    public static final int MINIMUM_INTERESTING_SCORE = 0;

    /**
     * A value, from 0 to 100, that indicates how similar the two submissions are to each other. A value of 100 indicates they are identical.
     */
    private int score;
    /**
     * Additional details about the results of the similarity comparison. This is usually the concatenation of comparison details of the individual documents in the submissions.
     */
    private String details;
    /**
     * The primary key value of the row that represents the older submission in the database table.
     */
    private final int oldSubmissionId;
    /**
     * The primary key value of the row that represents the newer submission in the database table.
     */
    private final int newSubmissionId;
    /**
     * A value indicating whether there is an extremely high chance that one of the two submissions is plagiarism.
     */
    private boolean suspicious;


    /**
     * Gets a value, from 0 to 100, that indicates how similar the two submissions are to each other. A value of 100 indicates they are identical.
     * @return A value, from 0 to 100, that indicates how similar the two submissions are to each other. A value of 100 indicates they are identical.
     */
    public int getScore() {
        return score;
    }

    /**
     * Sets a value, from 0 to 100, that indicates how similar the two submissions are to each other. A value of 100 indicates they are identical.
     * @param score A value, from 0 to 100, that indicates how similar the two submissions are to each other. A value of 100 indicates they are identical.
     */
    public void setScore(int score) {
        this.score = score;
    }

    /**
     * Gets additional details about the results of the similarity comparison. This is usually the concatenation of comparison details of the individual documents in the submissions.
     * @return Additional details about the results of the similarity comparison. This is usually the concatenation of comparison details of the individual documents in the submissions.
     */
    public String getDetails() {
        return details;
    }

    /**
     * Sets additional details about the results of the similarity comparison. This is usually the concatenation of comparison details of the individual documents in the submissions.
     * @param details Additional details about the results of the similarity comparison. This is usually the concatenation of comparison details of the individual documents in the submissions.
     */
    public void setDetails(String details) {
        this.details = details;
    }

    /**
     * Gets the primary key value of the row that represents the older submission in the database table.
     * @return The primary key value of the row that represents the older submission in the database table.
     */
    public int getOldSubmissionId() {
        return oldSubmissionId;
    }

    /**
     * Gets the primary key value of the row that represents the newer submission in the database table.
     * @return The primary key value of the row that represents the newer submission in the database table.
     */
    public int getNewSubmissionId() {
        return newSubmissionId;
    }

    /**
     * Gets a value indicating whether there is an extremely high chance that one of the two submissions is plagiarism.     *
     * @return A value indicating whether there is an extremely high chance that one of the two submissions is plagiarism.
     */
    public boolean isSuspicious() {
        return suspicious;
    }

    /**
     * Sets a value indicating whether there is an extremely high chance that one of the two submissions is plagiarism.     *
     * @param suspicious A value indicating whether there is an extremely high chance that one of the two submissions is plagiarism.
     */
    public void setSuspicious(boolean suspicious) {
        this.suspicious = suspicious;
    }

    /**
     * Initializes a new instance of the Similarity class - this class represents the results of similarity checking between two submissions.
     * @param score A value, from 0 to 100, that indicates how similar the two submissions are to each other. A value of 100 indicates they are identical.
     * @param details Additional details about the results of the similarity comparison. This is usually the concatenation of comparison details of the individual documents in the submissions.
     * @param oldSubmissionId The primary key value of the row that represents the older submission in the database table.
     * @param newSubmissionId The primary key value of the row that represents the newer submission in the database table.
     * @param suspicious A value indicating whether there is an extremely high chance that one of the two submissions is plagiarism.
     */
    public Similarity(int score, String details, int oldSubmissionId, int newSubmissionId, boolean suspicious) {
        this.score = score;
        this.details = details;
        this.oldSubmissionId = oldSubmissionId;
        this.newSubmissionId = newSubmissionId;
        this.suspicious = suspicious;
    }
}
