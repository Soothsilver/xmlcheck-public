package sooth.unittests;

import org.junit.Test;
import sooth.similarity.GreedyStringTilingAlgorithm;

import static org.junit.Assert.*;

@SuppressWarnings("ALL")
public class GreedyStringTilingAlgorithmTest {

    @Test
    public void testCompareDifferentLengths() throws Exception {
        GreedyStringTilingAlgorithm greedyStringTilingAlgorithm = new GreedyStringTilingAlgorithm(2);
        assertEquals(3, greedyStringTilingAlgorithm.compare("ABCDEFGH*XXX*IJKLMNO", "FUGDCB_XXX_") );
    }

    @Test
    public void testCompareMML2() throws Exception {
        GreedyStringTilingAlgorithm greedyStringTilingAlgorithm = new GreedyStringTilingAlgorithm(2);
        assertEquals(3, greedyStringTilingAlgorithm.compare("AAABC", "BAAAC"));
        assertEquals(0, greedyStringTilingAlgorithm.compare("A1A2B3B4C5C6D7D8E9E", "B*B*D*D*C*C*A*A*E*E"));
    }
    @Test
    public void testCompareMML0() throws Exception {
        GreedyStringTilingAlgorithm greedyStringTilingAlgorithm = new GreedyStringTilingAlgorithm(0);
        assertEquals(5, greedyStringTilingAlgorithm.compare("ABCDE", "EDCBA"));
    }
}