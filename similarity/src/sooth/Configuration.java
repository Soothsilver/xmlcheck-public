package sooth;

import org.ini4j.Profile;
import org.ini4j.Wini;

import java.io.File;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.util.Objects;

/**
 * Contains configuration information and information about filesystem paths.
 */
public class Configuration {

    /**
     * Parses a string as an integer or returns the default value if the parsing fails.
     * Source: http://stackoverflow.com/a/1486521/1580088
     * @param number The string to parse.
     * @param defaultValue The integer to return in case parsing fails.
     * @return Integer created by parsing the string, or the default value.
     */
    private static int parseIntegerWithDefault(String number, int defaultValue) {
        try {
            return Integer.parseInt(number);
        } catch (NumberFormatException e) {
            return defaultValue;
        }
    }

    /**
     * Loads configuration for the similarity module from the 'config.ini' file of the main application.
     * @param configurationFile The config.ini file loaded into a Wini object.
     */
    public static void loadFromConfigIni(Wini configurationFile)
    {
        // MySQL credentials
        Profile.Section sectionMySQL = configurationFile.get("database");
        dbHostname = clearQuotes(sectionMySQL.get("host"));
        dbUser = clearQuotes(sectionMySQL.get("user"));
        dbPassword = clearQuotes(sectionMySQL.get("pass"));
        dbDatabaseName = clearQuotes(sectionMySQL.get("db"));

        // Submissions folder
        submissionsFolder = System.getProperty("user.dir") + File.separator + ".." + File.separator + "files" + File.separator + "submissions";

        // Similarity settings
        Profile.Section section = configurationFile.get("similarity");
        String enableSimilarityCheckingValue = clearQuotes(section.get("enableSimilarityChecking"));
        String enableZhangShashaValue = clearQuotes(section.get("enableZhangShasha"));
        String levenshteinMasterThresholdValue = clearQuotes(section.get("levenshteinMasterThreshold"));
        String zhangShashaSuspicionThresholdValue = clearQuotes(section.get("zhangShashaSuspicionThreshold"));
        String levenshteinSuspicionThresholdValue = clearQuotes(section.get("levenshteinSuspicionThreshold"));

        if (enableSimilarityCheckingValue != null)
        {
            enableSimilarityChecking = Objects.equals(enableSimilarityCheckingValue.toLowerCase(), "true");
        }
        if (enableZhangShashaValue != null)
        {
            preprocessZhangShashaTrees = Objects.equals(enableZhangShashaValue.toLowerCase(), "true");
        }
        if (levenshteinMasterThresholdValue != null) {
            levenshteinMasterThreshold = parseIntegerWithDefault(levenshteinMasterThresholdValue, levenshteinMasterThreshold);
        }
        if (zhangShashaSuspicionThresholdValue != null) {
            zhangShashaSuspicionThreshold = parseIntegerWithDefault(zhangShashaSuspicionThresholdValue, zhangShashaSuspicionThreshold);
        }
        if (levenshteinSuspicionThresholdValue != null) {
            levenshteinSuspicionThreshold = parseIntegerWithDefault(levenshteinSuspicionThresholdValue, levenshteinSuspicionThreshold);
        }
        /*
        Example of the appropriate section in the INI file.
        [similarity]
        enableSimilarityChecking = true
        enableZhangShasha = true
        zhangShashaMasterThreshold = 70
        levenshteinMasterThreshold = 60
        zhangShashaSuspicionThreshold = 90
        levenshteinSuspicionThreshold = 80
        */
    }

    /**
     * Trims the given string and removes double quotes from it if there are double quotes. If the given string is null,
     * then null is returned.
     * @param iniValue A string to trim and remove quotes from.
     * @return The trimmed string.
     */
    private static String clearQuotes(String iniValue) {
        if (iniValue == null) { return null; }
        iniValue = iniValue.trim();
        if ((iniValue.charAt(0) == '"') && (iniValue.charAt(iniValue.length() - 1) == '"')) {
            iniValue = iniValue.substring(1, iniValue.length() - 1);
        }
        return iniValue;
    }

    /**
     * Indicates whether a similarity should be ignored if both submissions were submitted by the same student.
     */
    private static final boolean ignoreSelfPlagiarism = true;

    /**
     * Indicates whether Zhang-Shasha post-order tree representations should be generated from XML files during preprocessing.
     */
    public static boolean preprocessZhangShashaTrees = true;
    /**
     * Indicates whether similarity checking should be performed at all.
     */
    public static boolean enableSimilarityChecking = true;
    /**
     * If the similarity of two submissions is lesser than this, then it's not relevant at all and will not be put into database, unless it is above the Zhang-Shasha suspicion threshold.
     */
    public static int levenshteinMasterThreshold = 0;
    /**
     * If the similarity of two submission based on Zhang-Shasha is greater or equal to this value, it is put into database and marked as suspicious.
     */
    @SuppressWarnings("MagicNumber") // This is a default value that may be overridden in the config.ini file, thus it's not a constant.
    public static int zhangShashaSuspicionThreshold = 90;
    /**
     * If the similarity of two submission based on Levenshtein distance is greater or equal to this value, it is put into database and marked as suspicious.
     */
    @SuppressWarnings("MagicNumber") // This is a default value that may be overridden in the config.ini file, thus it's not a constant.
    public static int levenshteinSuspicionThreshold = 80;

    /**
     * Database server IP or hostname.
     */
    public static String dbHostname = "";
    /**
     * Database user.
     */
    public static String dbUser = "";
    /**
     * Database password.
     */
    public static String dbPassword = "";
    /**
     * Database name.
     */
    public static String dbDatabaseName = "";



    private static String submissionsFolder = "C:\\Apps\\UwAmp\\www\\xmlcheck\\www\\files\\submissions";


    /**
     * Sets the folder where submission ZIP files are located. This folder is then used by the similarity module to locate
     * files to extract into documents.
     * @param submissionsFolder Path to the submissions folder, without the trailing slash.
     */
    public static void setSubmissionsFolder(String submissionsFolder) {
        Configuration.submissionsFolder = submissionsFolder;
    }


    /**
     * Returns the absolute path to the specified submission.
     * @param submissionFile Path to the submission relative to the submission folder (exactly as it is in the database).
     * @return The absolute path to the specified submission.
     */
    public static Path getSubmissionInputPath(String submissionFile) {
        return Paths.get(submissionsFolder, submissionFile);
    }

    /**
     * Returns a value that indicates whether a similarity should be ignored if both submissions were submitted by the same student.
     * @return A value that indicates whether a similarity should be ignored if both submissions were submitted by the same student.
     */
    public static boolean ignoringSelfPlagiarism() {
        return ignoreSelfPlagiarism;
    }
}
