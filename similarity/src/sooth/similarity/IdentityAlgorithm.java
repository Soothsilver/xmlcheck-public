package sooth.similarity;

/**
 * This test checks whether the two documents are identical, character-by-character.
 * This was a simple test to verify basic functionality and is not actually used in the XML Check system.
 *
 * This test was used as only in early version of the similarity module and is retained only as a historical artifact.
 *
 * Best case complexity: O(1)
 * Worst case complexity: O(n)
 */
public class IdentityAlgorithm {
    /**
     * Compares the two strings for identity.
     *
     * This method is thread-safe.
     *
     * @param oldDocument Text of a document.
     * @param newDocument Text of the second document.
     * @return True, if the documents are identical. False otherwise.
     */
    public boolean compare(String oldDocument, String newDocument) {
       return oldDocument.equals(newDocument);
    }
}
