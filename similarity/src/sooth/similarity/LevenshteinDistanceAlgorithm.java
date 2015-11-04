package sooth.similarity;

import java.lang.reflect.Field;

/**
 * This test returns the Levenshtein distance of the two specified strings.
 *
 * Levenshtein distance is also called "edit distance". It is defined as the smallest number of "edit operations" that
 * must be performed on the first string to turn it into the second string.
 *
 * These "edit operations" are:
 * - Deletion of a character.
 * - Insertion of a character anywhere in the string.
 * - Replacement of a character by any other character.
 *
 * Time complexity: O(m*n)
 * Space complexity: O(max(m, n))
 *
 */
public class LevenshteinDistanceAlgorithm {

    /**
     * This is the size of the internal arrays when this class is instantiated.
     * The arrays automatically grow whenever a document is being parsed that is larger than their current size.
     *
     * Because frequent creation of new arrays may be costly, setting a default value higher than 1 speeds up the comparisons
     * a little.
     */
    private static final int DEFAULT_ARRAY_SIZE = 10000;
    /**
     * This thread-local instance field contains an instance of LevenshteinDistanceTest for each thread. In this way,
     * within a thread, LevenshteinDistanceTest is a singleton class.
     */
    private static final ThreadLocal<LevenshteinDistanceAlgorithm> instance = new ThreadLocal<LevenshteinDistanceAlgorithm>() {
        @Override
        protected LevenshteinDistanceAlgorithm initialValue() {
            return new LevenshteinDistanceAlgorithm();
        }
    };
    /**
     * First array representing a row of the dynamic programming table.
     */
    private int[] v0 = new int[DEFAULT_ARRAY_SIZE];
    /**
     * Second array representing a row of the dynamic programming table
     */
    private int[] v1 = new int[DEFAULT_ARRAY_SIZE];
    /**
     * This is the "value" private field of the String class. It is set by the constructor of the class. We use
     * this field (and Java reflection) because it's faster than using traditional access methods such as charAt.
     */
    private Field field = null;

    /**
     * Initializes a new instance of the LevenshteinDistanceTest class. This constructor is called only once per thread,
     * and this is done by the thread-local variable "instance".
     */
    private LevenshteinDistanceAlgorithm() {
        try {
            field = String.class.getDeclaredField("value");
            field.setAccessible(true);
        } catch (NoSuchFieldException ignored) {
           // Won't happen.
        }
    }

    /**
     * Returns the instance of LevenshteinDistanceTest for this thread. If the instance does not exist yet, it is created.
     * @return An instance of the LevenshteinDistanceTest class.
     */
    public static LevenshteinDistanceAlgorithm getInstance() {
        return instance.get();
    }

    /**
     * Compares the two strings using the Levenshtein distance algorithm and returns the Levenshtein distance of the two strings.
     *
     * The performance of this method does not depend on the order of the strings. This method is thread-safe.
     *
     * @param oldDocument The first string.
     * @param newDocument The second string.
     * @return Levenshtein distance of the two strings.
     */
    public int compare(String oldDocument, String newDocument)
    {
        int maxDistance = Math.max(oldDocument.length(), newDocument.length());
        if (v0.length < (maxDistance + 1))
        {
            v0 = new int[maxDistance + 1];
        }
        if (v1.length < (maxDistance + 1))
        {
            v1 = new int[maxDistance + 1];
        }

        int[] vCopyFrom = v0;
        int[] vCopyTo = v1;

        int newDocumentLength = newDocument.length();
        int oldDocumentLength = oldDocument.length();

        try {
            final char[] oldCharArray = (char[]) field.get(oldDocument);
            final char[] newCharArray = (char[]) field.get(newDocument);

            for (int i = 0; i <= newDocumentLength; i++) {
                vCopyFrom[i] = i;
            }

            for (int i = 0; i < oldDocumentLength; i++)
            {
                vCopyTo[0] = i + 1;

                for (int j = 0; j < newDocumentLength; j++)
                {
                    int cost = (oldCharArray[i] == newCharArray[j]) ? 0 : 1;
                    vCopyTo[j + 1] = Math.min(Math.min(vCopyTo[j] + 1, vCopyFrom[j + 1] + 1), vCopyFrom[j] + cost);
                }

                int[] vIntermediary = vCopyFrom;
                vCopyFrom = vCopyTo;
                vCopyTo = vIntermediary;
            }
            return vCopyFrom[newDocumentLength];
        } catch (IllegalAccessException ignored) {
            // Won't happen.
            throw new RuntimeException();
        }
    }
}
