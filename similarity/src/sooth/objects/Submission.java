package sooth.objects;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;

/**
 * Represents a single submission by a student.
 *
 * This class maps to a row of the submissions table in the database.
 */
public class Submission {
    private List<Document> documents = new ArrayList<>();
    private int submissionId = -1;
    private int userId = -1;
    private Date uploadTime = null;
    private final String pluginIdentifier;

    /**
     * Returns all loaded documents associated with this submission. A document is a file. Some documents such as
     * query files or DTD files are not loaded at all and thus won't be returned here.
     * @return A list of all loaded associated documents.
     */
    public List<Document> getDocuments() {
        return documents;
    }

    /**
     * Gets the database ID of the row this submission maps to.
     * @return A database row ID.
     */
    public int getSubmissionId() {
        return submissionId;
    }

    /**
     * Gets the database ID of the user who submitted this submission.
     * @return A database row ID.
     */
    public int getUserId() {
        return userId;
    }

    /**
     * Gets the time at which this submission was submitted.
     * @return The time of submission.
     */
    public Date getUploadTime() {
        return uploadTime;
    }

    /**
     * Gets the unique identifier of the plugin this submission is checked by.
     * @return The unique identifier of the plugin.
     */
    public String getPluginIdentifier() {
        return pluginIdentifier;
    }

    /**
     * Initializes a new instance of the Submission class.
     * @param documents A list of associated documents.
     * @param submissionId Database ID of the submission.
     * @param userId Database ID of its owner.
     * @param uploadTime Time of submission.
     * @param pluginIdentifier Unique identifier of associated plugin.
     */
    public Submission(List<Document> documents, int submissionId, int userId, Date uploadTime, String pluginIdentifier) {
        this.documents = documents;
        this.submissionId = submissionId;
        this.userId = userId;
        this.uploadTime = uploadTime;
        this.pluginIdentifier = pluginIdentifier;
    }
}
