package sooth.unittests;

import org.junit.Test;
import sooth.similarity.IdentityAlgorithm;
import static org.junit.Assert.*;

@SuppressWarnings("ALL")
public class IdentityAlgorithmTest {

    @Test
    public void testCompare() throws Exception {
        IdentityAlgorithm test = new IdentityAlgorithm();
        assertTrue(test.compare("SIMPLE TEXT", "SIMPLE TEXT"));
        assertTrue(test.compare("", ""));
        assertFalse(test.compare("SIMPLE", "ADVANCED"));
        assertFalse(test.compare("12345", "12346"));
    }
}