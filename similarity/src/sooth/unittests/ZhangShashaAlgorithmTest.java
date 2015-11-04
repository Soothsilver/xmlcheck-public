package sooth.unittests;

import org.junit.Test;
import org.w3c.dom.Document;
import org.xml.sax.InputSource;
import sooth.similarity.ZhangShashaAlgorithm;
import sooth.similarity.ZhangShashaTree;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import java.io.StringReader;

import static org.junit.Assert.assertEquals;

@SuppressWarnings("ALL")
public class ZhangShashaAlgorithmTest {
    public ZhangShashaTree treeFromFragment(String xml) throws Exception {
        return new ZhangShashaTree(loadXMLFromString("<?xml version='1.0'?>" + xml));
    }
    // http://stackoverflow.com/q/33262
    public Document loadXMLFromString(String xml) throws Exception
    {
        DocumentBuilderFactory factory = DocumentBuilderFactory.newInstance();
        factory.setNamespaceAware(true);
        DocumentBuilder builder = factory.newDocumentBuilder();

        return builder.parse(new InputSource(new StringReader(xml)));
    }

    @Test
    public void testKeyroots() throws Exception {
        ZhangShashaTree tree = treeFromFragment("<f><d><a /><c><b /></c></d><e/></f>");
        assertEquals(3, tree.keyroots.size());
        assertEquals((Integer)2, tree.keyroots.get(0));
        assertEquals((Integer)4, tree.keyroots.get(1));
        assertEquals((Integer)5, tree.keyroots.get(2));
    }

    @Test
    public void testLeftmostLeaf() throws Exception {
        ZhangShashaTree tree = treeFromFragment("<nine><three><one /><two /></three><eight><six><four /><five /></six><seven /></eight></nine>");
        assertEquals(9, tree.getNodeCount());
        assertEquals(0, tree.nodes.get(0).getLeftmostLeaf());
        assertEquals(1, tree.nodes.get(1).getLeftmostLeaf());
        assertEquals(0, tree.nodes.get(2).getLeftmostLeaf());
        assertEquals(3, tree.nodes.get(3).getLeftmostLeaf());
        assertEquals(4, tree.nodes.get(4).getLeftmostLeaf());
        assertEquals(3, tree.nodes.get(5).getLeftmostLeaf());
        assertEquals(6, tree.nodes.get(6).getLeftmostLeaf());
        assertEquals(3, tree.nodes.get(7).getLeftmostLeaf());
        assertEquals(0, tree.nodes.get(8).getLeftmostLeaf());
    }

    @Test
    public void testLabel() throws Exception {

        ZhangShashaTree tree = treeFromFragment("<nine><three><one /><two /></three><eight><six><four /><five /></six><seven /></eight></nine>");
        assertEquals(9, tree.getNodeCount());
        assertEquals("one", tree.nodes.get(0).getLabel());
        assertEquals("two", tree.nodes.get(1).getLabel());
        assertEquals("three", tree.nodes.get(2).getLabel());
        assertEquals("four", tree.nodes.get(3).getLabel());
        assertEquals("five", tree.nodes.get(4).getLabel());
        assertEquals("six", tree.nodes.get(5).getLabel());
        assertEquals("seven", tree.nodes.get(6).getLabel());
        assertEquals("eight", tree.nodes.get(7).getLabel());
        assertEquals("nine", tree.nodes.get(8).getLabel());
    }

    @Test
    public void testSimple1() throws Exception {
        ZhangShashaTree one = treeFromFragment("<one><two /></one>");
        ZhangShashaTree two = treeFromFragment("<one><three /></one>");
        assertEquals(ZhangShashaAlgorithm.RELABEL_COST, ZhangShashaAlgorithm.getInstance().compare(one, two));
    }
    @Test
    public void testSimple2() throws Exception {
        ZhangShashaTree one = treeFromFragment("<one><two /></one>");
        ZhangShashaTree two = treeFromFragment("<one><three><four /></three></one>");
        assertEquals(ZhangShashaAlgorithm.RELABEL_COST + ZhangShashaAlgorithm.INSERTION_COST, ZhangShashaAlgorithm.getInstance().compare(one, two));
    }
    @Test
    public void testSimple3() throws Exception {
        ZhangShashaTree one = treeFromFragment("<one><two /></one>");
        ZhangShashaTree two = treeFromFragment("<four><one><two />     </one></four>");
        assertEquals(ZhangShashaAlgorithm.INSERTION_COST, ZhangShashaAlgorithm.getInstance().compare(one, two));
    }
    @Test
    public void testSimple4() throws Exception {
        ZhangShashaTree one = treeFromFragment("<one><two /></one>");
        ZhangShashaTree two = treeFromFragment("<one />");
        assertEquals(ZhangShashaAlgorithm.DELETION_COST, ZhangShashaAlgorithm.getInstance().compare(one, two));
    }

    @Test
    public void testFromArticle() throws Exception {
        ZhangShashaTree one = treeFromFragment("<f><d><a /><c><b /></c></d><e /></f>");
        ZhangShashaTree two = treeFromFragment("<f><c><d><a /><b /></d></c><e /></f>");
        assertEquals(ZhangShashaAlgorithm.DELETION_COST + ZhangShashaAlgorithm.INSERTION_COST, ZhangShashaAlgorithm.getInstance().compare(one, two));

    }
}