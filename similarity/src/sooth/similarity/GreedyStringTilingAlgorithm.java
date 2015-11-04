package sooth.similarity;

import java.util.ArrayList;
import java.util.List;

/**
 * This test returns the number of characters in tiles found by the Greedy-String-Tiling algorithm.
 *
 * The Greedy-String-Tiling algorithm finds the largest common substrings from the two specified strings and "marks"
 * them. It then repeats this process, ignoring marked substrings until the only characters that remain unmarked
 * don't form any common substring with a length greater or equal to the MinimumMatchLength. More information on this
 * algorithm is available in the attached thesis or in the original paper presenting this algorithm,
 * "String Similarity via Greedy String Tiling and Running Karp−Rabin Matching" by Michael J. Wise.
 *
 * Best case complexity: O(m*n)
 * Worst case complexity: O(m*n*n)
 * Space complexity: O(m+n)
 *
 */
public class GreedyStringTilingAlgorithm {
    /**
     * Represents a common substring among two strings.
     */
    private static class Match
    {
        /**
         * The position where the common substring starts in the shorter of the two strings.
         */
        public final int PatternStart;
        /**
         * The position where the common substring starts in the longer of the two strings.
         */
        public final int DocumentStart;
        /**
         * Length of the common substring.
         */
        public final int Length;

        /**
         * Initializes an instance of the Match class which represents a common substring among two strings.
         * @param startPattern The position where the common substring starts in the shorter of the two strings.
         * @param startDocument The position where the common substring starts in the longer of the two strings.
         * @param length Length of the common substring.
         */
        public Match(int startPattern, int startDocument, int length) {
            PatternStart = startPattern;
            DocumentStart = startDocument;
            Length = length;
        }
    }

    /**
     * Specifies the MinimumMatchLength, which is a parameter of the Greedy-String-Tiling algorithm. A brief explanation is that only substrings greater or equal to this length can be considered tiles.
     */
    private int MinimumMatchLength = 8;

    /**
     * Initializes a new instance of the GreedyStringTilingAlgorithm class.
     * @param minimumMatchLength Specifies the MinimumMatchLength, which is a parameter of the Greedy-String-Tiling algorithm. A brief explanation is that only substrings greater or equal to this length can be considered tiles.
     */
    public GreedyStringTilingAlgorithm(int minimumMatchLength) {
        this.MinimumMatchLength = minimumMatchLength;
    }
    /**
     * Initializes a new instance of the GreedyStringTilingAlgorithm class with the default MinimumMatchLength.
     */
    public GreedyStringTilingAlgorithm() {  }

    /**
     * Compares the two strings using the Greedy-String-Tiling algorithm and returns the number of characters that were matched in tiles.
     *
     * The performance of this method does not depend on the order of the string. This method is thread-safe.
     *
     * @param one The first string.
     * @param two The second string.
     * @return Total number of characters in common substrings identified as tiles by the algorithm.
     */
    @SuppressWarnings("ConstantConditions")
    public int compare(String one, String two)
    {
        // In this degenerate case, we consider there to be no similarity between the documents.
        if ((one.isEmpty()) || (two.isEmpty()))
        {
            return 0;
        }

        // The shorter string will be referred to as the "pattern". The longer string will be the "document".
        String pattern = one;
        String document = two;
        if (one.length() > two.length())
        {
            pattern = two;
            document = one;
        }

        int lengthOfTokensTiled = 0;
        final char[] patternChars = getCharacterArrayFromString(pattern);
        final char[] documentChars = getCharacterArrayFromString(document);

        int firstUnmarkedPatternToken = 0;
        int firstUnmarkedDocumentToken = 0;
        List<Match> matches = new ArrayList<>();
        // This list exists only for debugging purposes:
        // List<Match> tilesFound = new ArrayList<>();
        int maxmatch;
        do {
            maxmatch = MinimumMatchLength;
            matches.clear();

            // Phase 1: Search for the largest common substring of unmarked characters
            for (int i = firstUnmarkedPatternToken; i < pattern.length(); i++) {
                for (int j = firstUnmarkedDocumentToken; j < document.length(); j++) {
                    int k = 0;
                    while (true)
                    {
                        if (((i + k) == pattern.length()) || ((j + k) == document.length())) {
                            break;
                        }

                        char patternToken = patternChars[i + k];
                        char documentToken = documentChars[j + k];
                        if (patternToken != documentToken) { break; }
                        if (patternToken == MARKED_CHARACTER) { break; }
                        if (documentToken == MARKED_CHARACTER) { break; }
                        k++;
                    }
                    if (k == maxmatch)
                    {
                        matches.add(new Match(i, j, k));
                    }
                    else if (k > maxmatch)
                    {
                        matches.clear();
                        matches.add(new Match(i, j, k));
                        maxmatch = k;
                    }

                }
            }
            // Phase 2: Marking tiles
            for(Match match : matches) {

                // Occlusion test
                if (patternChars[match.PatternStart] == MARKED_CHARACTER) { continue; }
                if (patternChars[(match.PatternStart + match.Length) - 1] == MARKED_CHARACTER) { continue; }
                if (documentChars[match.DocumentStart] == MARKED_CHARACTER) { continue; }
                if (documentChars[(match.DocumentStart + match.Length) - 1] == MARKED_CHARACTER) { continue; }

                // Move the "first unmarked" pointers to the right
                if (match.PatternStart == firstUnmarkedPatternToken) { firstUnmarkedPatternToken = match.PatternStart + match.Length; }
                if (match.DocumentStart == firstUnmarkedDocumentToken) { firstUnmarkedDocumentToken = match.DocumentStart + match.Length; }

                // Mark the tile
                for (int j = 0; j < match.Length; j++) {
                    patternChars[match.PatternStart + j] = MARKED_CHARACTER;
                    documentChars[match.DocumentStart + j] = MARKED_CHARACTER;
                }

                lengthOfTokensTiled += match.Length;

                // For debugging only:
                // tilesFound.add(match);
                // Debugging print:
                // System.out.println("Match found (pattern " + match.PatternStart + "-" + (match.PatternStart + match.Length - 1) + ", '" + pattern.substring(match.PatternStart, match.PatternStart + match.Length - 1) + "')");
            }
        }
        while (maxmatch > MinimumMatchLength);
        return lengthOfTokensTiled;
    }

    /**
     * Converts the string to a newly allocated character array.
     *
     * This is in a special function because previously, we used Reflection to access this array instead. However,
     * it appears using Reflection here does not bring any significant speed-up (because we need to modify the array
     * as well), and is more complex so we're not using it anymore.
     *
     * @param text String to convert to a character array.
     * @return The character array that is now independent of the string.
     */
    private char[] getCharacterArrayFromString(String text) {
        return text.toCharArray();
    }

    /**
     * This character represents a character marked by the Greedy-String-Tiling algorithm.
     *
     * We do not need to know what the actual character was after it's marked. We can get significant performance gains
     * by putting the mark directly into the character array rather than having an array of objects on the heap.
     *
     * The actual value of this constant doesn't matter except that it should not occur in the texts. If it occurs there,
     * the test might return a lesser similarity value but will not crash. The START OF HEADING character (0001) cannot
     * legally occur in XML text and we trust that Java code of most student will not contain this character.
     */
    private static final char MARKED_CHARACTER = '\u0001';
}
