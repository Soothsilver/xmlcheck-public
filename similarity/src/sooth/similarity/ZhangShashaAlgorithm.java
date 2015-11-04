package sooth.similarity;

/**
 * This test returns the number of tree edit operations that must be performed on a tree to transform it into the other tree.
 *
 * This algorithm is introduced in the article "Simple fast algorithms for the editing distance between trees and related problems"
 * by Kaizhong Zhang and Dennis Shasha. It uses dynamic programming to find the minimum edit script using the operations
 * "insert a node", "delete a node" and "relabel a node".
 *
 * Time complexity: O(m*n*min(depth(T1), leaves(T1))*min(depth(T2), leaves(T2)).
 * Worst case time complexity is therefore: O(m*m*n*n)
 * Space complexity: O(m*n)
 */
public class ZhangShashaAlgorithm {
    /**
     * This thread-local instance field contains an instance of ZhangShashaAlgorithm for each thread. In this way,
     * within a thread, ZhangShashaAlgorithm is a singleton class.
     */
    private static final ThreadLocal<ZhangShashaAlgorithm> instance = new ThreadLocal<ZhangShashaAlgorithm>() {
        @Override
        protected ZhangShashaAlgorithm initialValue() {
            return new ZhangShashaAlgorithm();
        }
    };
    /**
     * Returns the instance of ZhangShashaAlgorithm for this thread. If the instance does not exist yet, it is created.
     * @return An instance of the ZhangShashaAlgorithm class.
     */
    public static ZhangShashaAlgorithm getInstance() {
        return instance.get();
    }
    /**
     * Cost to delete one node from the first tree.
     */
    public static final int DELETION_COST = 1;
    /**
     * Cost to insert one node to the second tree.
     */
    public static final int INSERTION_COST = 1;
    /**
     * Cost to relabel one node.
     */
    public static final int RELABEL_COST = 1;

    private int[][] treedist = new int[1][1];
    private int[][] forestdist= new int[1][1];
    private ZhangShashaTree firstTree;
    private ZhangShashaTree secondTree;

    /**
     * Compares two labeled ordered trees and returns their Zhang-Shasha tree edit distance.
     *
     * @param one The first tree.
     * @param two The second tree.
     * @return The tree edit distance.
     */
    public int compare(ZhangShashaTree one, ZhangShashaTree two) {
        if ((treedist.length <= one.getNodeCount()) || (treedist.length <= two.getNodeCount()))
        {
            int higherNodeCount = Math.max(one.getNodeCount(), two.getNodeCount()) + 1;
            treedist = new int[higherNodeCount][higherNodeCount];
            forestdist = new int[higherNodeCount][higherNodeCount];
        }

        firstTree = one;
        secondTree = two;

        for(int i : one.keyroots)
        {
            for (int j : two.keyroots)
            {
                calculateTreeDist(i, j);
            }
        }

        return treedist[one.getNodeCount()][two.getNodeCount()];
    }

    /**
     * Returns the minimum of three values. This function is needed in the Zhang-Shasha algorithm.
     * @param a The first value.
     * @param b The second value.
     * @param c The third value.
     * @return Minimum of the values.
     */
    private int min(int a, int b, int c)
    {
        return Math.min(a, Math.min(b, c));
    }
    /**
     * Calculates part of  tree distance matrix as per the paper.
     * forestdist is indexed in this way:
     * 0 means the empty set
     * An integer "k" means the set from l(i) to l(i)+k.
     * @param iMajor Index of a keyroot in the tree (zero-based).
     * @param jMajor Index of a keyroot in the second tree (zero-based).
     */
    @SuppressWarnings("UnclearBinaryExpression") // This method is much clearer without the additional parentheses, thank you very much.
    private void calculateTreeDist(int iMajor, int jMajor) {
        forestdist[0][0] = 0;
        int li = firstTree.nodes.get(iMajor).getLeftmostLeaf() + 1; // one-based
        int lj = secondTree.nodes.get(jMajor).getLeftmostLeaf() + 1; // one-based
        iMajor++; // one-based
        jMajor++; // one-based
        int m = iMajor - li + 1;
        int n = jMajor - lj + 1;
        for (int k = 1; k <= m; k++) {
            forestdist[k][0] = forestdist[k-1][0] + DELETION_COST;
        }
        for (int k = 1; k <= n; k++) {
            forestdist[0][k] = forestdist[0][k-1] + INSERTION_COST;
        }
        for (int x = 1; x <= m; x++) {
            for (int y = 1; y <= n; y++) {
                int lx = firstTree.nodes.get(li - 1 + x - 1).getLeftmostLeaf() + 1;
                int ly = secondTree.nodes.get(lj - 1 + y - 1).getLeftmostLeaf() + 1;
                // x ... number of positions east of l(i)-1 (i.e. x=1 ... l(i))
                if (lx == li && ly == lj) {
                    forestdist[x][y] = min(
                            forestdist[x - 1][y] + DELETION_COST,
                            forestdist[x][y - 1] + INSERTION_COST,
                            forestdist[x - 1][y - 1] + (firstTree.nodes.get(li - 1 + x - 1).getLabel().equals(secondTree.nodes.get(lj - 1 + y - 1).getLabel()) ? 0 : RELABEL_COST)
                    );
                    treedist[li + x - 1][lj + y - 1] = forestdist[x][y];
                } else {
                    forestdist[x][y] = min(
                            forestdist[x - 1][y] + DELETION_COST,
                            forestdist[x][y - 1] + INSERTION_COST,
                            forestdist[lx - li + 1 - 1][ly - lj + 1 - 1] + treedist[li + x - 1][lj + y - 1]
                    );
                }
            }
        }
    }
}
