package sooth.scripts;

import org.jooq.DSLContext;
import sooth.Configuration;
import sooth.FilesystemUtils;
import sooth.Logging;
import sooth.Problems;
import sooth.connection.Database;
import sooth.entities.Tables;
import sooth.entities.tables.records.PluginsRecord;
import sooth.entities.tables.records.SubmissionsRecord;
import sooth.objects.Document;

import java.io.File;
import java.io.IOException;
import java.nio.file.Files;
import java.nio.file.Path;
import java.util.ArrayList;
import java.util.List;
import java.util.Objects;
import java.util.logging.Logger;

/**
 * This class contains functions that load documents (files) from submissions' ZIP files into the database.
 */
public class DocumentExtractor {
    private static final int MAXIMUM_DOCUMENT_SIZE = 50000; // 50 kB
    private static final int MAXIMUM_NUMBER_OF_SAME_DOCUMENT_TYPE = 3;

    private static final Logger logger = Logging.getLogger(DocumentExtractor.class.getName());

    /**
     * Returns a list of documents (non-preprocessed) contained in the ZIP file.
     * @param pathToZipFile Path to the ZIP file.
     * @param pluginIdentifier Unique string identifier of the plugin that is supposed to evaluate this submission.
     * @return The (possibly empty) list of documents.
     */
    private static List<Document> getDocumentsFromZipArchive(Path pathToZipFile, String pluginIdentifier) {
        ArrayList<Document> documents = new ArrayList<>();
        if (!Files.exists(pathToZipFile)) {
            logger.warning("The specified path '" + pathToZipFile + "' does not lead to a file.");
            return documents;
        }
        File temporaryFolder = null;
        try {
            temporaryFolder = FilesystemUtils.createTempDirectory();
            FilesystemUtils.unzip(pathToZipFile.toFile(), temporaryFolder);
            documents = getDocumentsFromDirectoryContents(temporaryFolder, pluginIdentifier);
        } catch (IOException e) {
            logger.warning("The file '" + pathToZipFile + "' could not be extracted to a temporary folder.");
        }
        FilesystemUtils.removeDirectoryAndContents(temporaryFolder);
        return documents;
    }

    /**
     * Returns a list of documents (non-preprocessed) contained in the directory.
     * @param submissionDirectory Path to the directory.
     * @param pluginIdentifier Unique string identifier of the plugin that is supposed to evaluate this submission.
     * @return The (possibly empty) list of documents.
     */
    private static ArrayList<Document> getDocumentsFromDirectoryContents(File submissionDirectory, String pluginIdentifier) {
        ArrayList<Document> documents = new ArrayList<>();
        List<File> files = getAbsoluteFilesRecursively(submissionDirectory);

        // Extracting individual files.
        for (File file : files) {
            String extension = FilesystemUtils.getFileExtension(file).toLowerCase();
            if (file.getPath().contains("MACOSX")) {
                // Files in the __MACOSX folder will not be checked for plagiarism, because they are metadata created by
                // Macintosh computers.
                continue;
            }

            Document.DocumentType type = getDocumentTypeFromExtension(pluginIdentifier, file, extension);
            if (type == null)
            {
                continue;
            }
            if ((type == Document.DocumentType.DTD_FILE) ||
                    (type == Document.DocumentType.XQUERY_QUERY) ||
                    (type == Document.DocumentType.XQUERY_ADDITIONAL_XML_FILE) ||
                    (type == Document.DocumentType.XPATH_QUERY)) {
                // These files are too small to be compared for similarity.
                continue;
            }

            int sameDocumentsPresent = 0;
            for(Document d : documents) {
                if (d.getType().equals(type)) {
                    if (type.canBePresentOnlyOnce()) {
                        logger.warning("In this submission, a document of the same type is already present. (present: " + d.getName() + ", being added: " + file.getName() + ")");
                    }
                    sameDocumentsPresent++;
                }
            }
            if (sameDocumentsPresent >= MAXIMUM_NUMBER_OF_SAME_DOCUMENT_TYPE) {
                logger.warning("There are too many of these files in the submission. Ignoring the rest.");
                continue;
            }

            try {
                String fileContents = FilesystemUtils.loadTextFile(file);
                if (fileContents.length() > MAXIMUM_DOCUMENT_SIZE)
                {
                    logger.warning("This document is too large. It won't be loaded in the database.");
                }
                else {
                    Document thisDocument = new Document(type, fileContents, file.getName());
                    documents.add(thisDocument);
                }
            } catch (IOException e) {
                logger.warning("This document could not be read from disk.");
            }

        }

        return documents;
    }

    /**
     * Determines document type from file extension and plugin identifier.
     * @param pluginIdentifier Identifier of the plugin that's supposed to evaluate this file.
     * @param file Any file in the submission.
     * @param extension The file's extension  (or empty string if it has no extension). An extension is the suffix after the last dot.
     * @return The document type, or null if the extension does not match any rule.
     */
    private static Document.DocumentType getDocumentTypeFromExtension(String pluginIdentifier, File file, String extension) {
        Document.DocumentType type = null;
        switch (extension) {
            case "xml":
                if (Objects.equals(pluginIdentifier, Problems.HW5_XQUERY))
                {
                    type = Document.DocumentType.XQUERY_ADDITIONAL_XML_FILE;
                }
                else {
                    type = Document.DocumentType.PRIMARY_XML_FILE;
                }
                break;
            case "dtd":
                type = Document.DocumentType.DTD_FILE;
                break;
            case "xq":
                type = Document.DocumentType.XQUERY_QUERY;
                break;
            case "xp":
                type = Document.DocumentType.XPATH_QUERY;
                break;
            case "xsd":
                type = Document.DocumentType.XSD_SCHEMA;
                break;
            case "xsl":
                type = Document.DocumentType.XSLT_SCRIPT;
                break;
            case "java":
                if (file.getName().equals("MyDomTransformer.java")) {
                    type = Document.DocumentType.JAVA_DOM_TRANSFORMER;
                }
                else if (file.getName().equals("MySaxHandler.java")) {
                    type = Document.DocumentType.JAVA_SAX_HANDLER;
                }
                else {
                    logger.fine("This submission contains a java source file other than the two permitted files ("+file.getName()+").");
                }
                break;
            default:
                // This may be an additional text file or something, we don't want to check that
                logger.fine("Unknown file extension: " + extension);
                break;
        }
        return type;
    }

    /**
     * Returns a list of absolute filenames contained in a directory by recursively searching it.
     * @param directory The directory to search (must be a directory).
     * @return The list of absolute filenames.
     */
    private static List<File> getAbsoluteFilesRecursively(File directory) {
        ArrayList<File> files = new ArrayList<>();
        //noinspection ConstantConditions
        for ( File file : directory.listFiles()) {
            files.add(file.getAbsoluteFile());
            if (file.isDirectory()) {
                files.addAll(getAbsoluteFilesRecursively(file));
            }
        }
        return files;
    }

    /**
     * Runs a jOOQ query that returns the record for the plugin that is supposed to evaluate the supplied submission.
     * @param submission The submission.
     * @return The plugin record.
     */
    private static PluginsRecord getPluginsRecordFromSubmissionsRecord(SubmissionsRecord submission)
    {
        DSLContext context = Database.getContext();
        return context.selectFrom(Tables.PLUGINS)
                .where(Tables.PLUGINS.ID.in(
                        context.select(Tables.PROBLEMS.PLUGINID).from(Tables.PROBLEMS)
                                .where(Tables.PROBLEMS.ID.in(
                                        context.select(Tables.ASSIGNMENTS.PROBLEMID).from(Tables.ASSIGNMENTS)
                                                .where(Tables.ASSIGNMENTS.ID.equal(submission.getAssignmentid())).fetch()
                                )).fetch()
                )).fetchAny();
    }

    /**
     * Extracts all relevant documents from the ZIP file associated with the specified submissions and puts those documents into the database.
     * @param submission The submission record identifying the submission whose documents should be put into the database.
     */
    public static void createDatabaseDocumentsFromSubmissionRecord(SubmissionsRecord submission) {
        PluginsRecord plugin = getPluginsRecordFromSubmissionsRecord(submission);
        if (plugin == null) {
            logger.info("The submission " + submission.getId() + " is not associated with any plugin. Skipping it.");
            return;
        }
        Path submissionInputPath = Configuration.getSubmissionInputPath(submission.getSubmissionfile());
        List<Document> documents = DocumentExtractor.getDocumentsFromZipArchive(submissionInputPath, plugin.getIdentifier());
        logger.fine("Submission '" + submission.getId() + "' generated " + documents.size() + " documents.");
        DSLContext context = Database.getContext();
        for(Document document : documents) {
            context.insertInto(Tables.DOCUMENTS, Tables.DOCUMENTS.TEXT,Tables.DOCUMENTS.NAME, Tables.DOCUMENTS.TYPE, Tables.DOCUMENTS.SUBMISSIONID)
                    .values(document.getText(), document.getName(), document.getType().getMysqlIdentifier(), submission.getId())
                    .execute();
            logger.fine("Document named '" + document.getName() + "' for submission '" + submission.getId() + "' was inserted into the database.");
        }
    }
}
