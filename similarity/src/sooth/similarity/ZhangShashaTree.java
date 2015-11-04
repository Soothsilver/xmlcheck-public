package sooth.similarity;

import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;

import java.util.ArrayList;
import java.util.List;

/**
 * Represents a labeled ordered tree, usually created from an XML document. This representation is used in the Zhang-Shasha algorithm.
 */
public class ZhangShashaTree {
    /**
     * A list of keyroots of this tree. See the original article by Zhang and Shasha for more details.
     */
    public final List<Integer> keyroots = new ArrayList<>();
    /**
     * A list of all nodes in this tree sorted by postorder.
     */
    public final List<ZhangShashaNode> nodes = new ArrayList<>();


    /**
     * Creates a new labeled ordered tree from an XML document. Text nodes that contain only whitespace are ignored.
     * The labels of nodes are taken from the names of elements and attributes, but comments and text nodes have no labels.
     * @param xmlDocument The DOM document that is to be transformed into a labeled ordered tree.
     */
    public ZhangShashaTree(Document xmlDocument) {
        Element documentElement = xmlDocument.getDocumentElement();
        enterDfs(documentElement);
        keyroots.add(nodes.size() - 1);
    }

    /**
     * Recursive function that adds information about the specified node and all its children into the tree. However,
     * it will not add the specified node to the keyroots array (because it does not know if it has any left siblings.
     * @param item A DOM node to analyze.
     * @return A ZhangShashaNode representing the specified DOM node.
     */
    private ZhangShashaNode enterDfs(Node item) {
        // Enter into sons
        NodeList children = item.getChildNodes();
        boolean isFirstChild = true;
        int leftmostLeafOfThisNodeIs = nodes.size();
        for (int i = 0; i < children.getLength(); i++) {
            Node childNode = children.item(i);
            if ((childNode.getNodeType() == Node.TEXT_NODE) && childNode.getNodeValue().trim().isEmpty()) {
                continue; // Ignoring whitespace nodes.
            }
            ZhangShashaNode zhangShashaChild = enterDfs(childNode);
            if (isFirstChild) {
                leftmostLeafOfThisNodeIs = zhangShashaChild.getLeftmostLeaf();
                isFirstChild = false;
            }
            else {
                keyroots.add(nodes.size() - 1);
            }
        }
        // Returns the Zhang-Shasha node
        ZhangShashaNode newNode = new ZhangShashaNode(leftmostLeafOfThisNodeIs, item.getNodeName());
        nodes.add(newNode);
        return newNode;
    }

    /**
     * Gets the number of nodes in this labeled ordered tree.
     * @return The number of nodes in this labeled ordered tree.
     */
    public int getNodeCount() {
        return nodes.size();
    }

    /**
     * Represents a node of a labeled ordered tree. A node typically represents an XML element, attribute or comment.
     */
    public static class ZhangShashaNode {

        private final int leftmostLeaf;
        private final String label;

        /**
         * Initializes a new instance of the Zhang-Shasha node class.
         * @param leftmostLeaf The postorder index of the leftmost leaf that is a son of this node.
         * @param label Name of this node.
         */
        public ZhangShashaNode(int leftmostLeaf, String label) {
            this.leftmostLeaf = leftmostLeaf;
            this.label = label;
        }

        /**
         * Gets the postorder index of the leftmost leaf that is a son of this node.
         * @return The postorder index of the leftmost leaf that is a son of this node.
         */
        public int getLeftmostLeaf() {
            return leftmostLeaf;
        }

        /**
         * Gets the name of this node.
         * @return Name of this node.
         */
        public String getLabel() {
            return label;
        }
    }
}
