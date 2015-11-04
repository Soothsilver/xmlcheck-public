package sooth.scripts;

/**
 * This class contains static functions useful in preprocessing documents.
 */
public class PreprocessingUtilities {

    /**
     * Removes whitespace from the specified string.
     * @param text The string to remove whitespace from.
     * @return The string with whitespace removed.
     */
    public static String removeWhitespace(String text) {
        return text.replaceAll("\\s+", "");
    }

    /**
     * Removes whitespace from each string in the array and returns an array with these modified strings.
     * @param stringsToRemoveWhitespaceFrom The array of strings where whitespace should be removed.
     * @return An array of the same size as the argument, containing the same strings in the same order, except that they have not whitespace.
     */
    public static String[] removeWhitespace(String[] stringsToRemoveWhitespaceFrom) {
        String[] result = new String[stringsToRemoveWhitespaceFrom.length];
        for (int i =0;i < stringsToRemoveWhitespaceFrom.length; i++)
        {
            result[i] = PreprocessingUtilities.removeWhitespace(stringsToRemoveWhitespaceFrom[i]);
        }
        return result;
    }

    /**
     * Removes all instances of strings in the supplied array from the supplied string.
     * @param haystack The string to remove needles from.
     * @param needles The strings to remove from the haystack.
     * @return The string with needles removed.
     */
    public static String removeSubstrings(String haystack, String[] needles) {
        // This might benefit from a better algorithm such as Aho-Corasick.
        // It's something to consider if this becomes a bottleneck.
        for(String needle : needles) {
            haystack = haystack.replace(needle, "");
        }
        return haystack;
    }



}
