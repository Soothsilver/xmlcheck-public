package sooth.unittests;

import org.junit.Test;
import sooth.similarity.LevenshteinDistanceAlgorithm;

import static org.junit.Assert.*;

@SuppressWarnings("ALL")
public class LevenshteinDistanceAlgorithmTest {

    @Test
    public void testCompare() throws Exception {
        LevenshteinDistanceAlgorithm levenshteinDistanceAlgorithm = LevenshteinDistanceAlgorithm.getInstance();
        assertEquals(0, levenshteinDistanceAlgorithm.compare("DOCUMENT", "DOCUMENT"));
        assertEquals(8, levenshteinDistanceAlgorithm.compare("DOCUMENT", "document"));
        assertEquals(1, levenshteinDistanceAlgorithm.compare("DOCuMENT", "DOCUMENT"));
        assertEquals(2, levenshteinDistanceAlgorithm.compare("DOC UM ENT", "DOCUMENT"));
        assertEquals(4, levenshteinDistanceAlgorithm.compare("DaCUEMNTs", "DOCUMENT"));
    }
}