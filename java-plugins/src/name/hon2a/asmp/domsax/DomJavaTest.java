package name.hon2a.asmp.domsax;

import name.hon2a.asm.TestCodeException;
import name.hon2a.asm.TestDataException;
import name.hon2a.asm.TestException;
import org.w3c.dom.Document;

import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerConfigurationException;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.dom.DOMSource;
import javax.xml.transform.stream.StreamResult;
import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.PrintStream;
import java.util.HashMap;
import java.util.Map;

/**
 * Test that compiles supplied XML transformation script in Java and runs it on
 * supplied XML file, saving transformed document to output file.
 *
 * @author hon2a
 */
public class DomJavaTest extends JavaTest {

	/**
	 * Source ID of Java files.
	 */
	public static final String sourceJava = "javaFiles";
	/**
	 * Source ID of xml document.
	 */
	public static final String sourceXml = "xmlDocument";
	/**
	 * Param ID of main class of user script
	 */
	public static final String paramDomScript = "userClass";
	/**
	 * Param ID of output file path
	 */
	public static final String paramOutputFile = "outputFile";
	/**
	 * Goal ID of transform xml goal
	 */
	private static final String goalTransformXml = "transform";

	/**
	 * Name of main method (access point) of user DOM script
	 */
	private static final String domScriptMainMethod = "transform";

	/**
	 * Initializes a new instance of DomJavaTest.
	 * @param sources Required sourceS: DomJavaTest::sourceJava, DomJavaTest::sourceXml
	 * @param params required parameters: DomJavaTest::paramDomScript, DomJavaTest::paramOutputFile
	 * @param outputFolder Folder to put output files into.
	 */
	public DomJavaTest (Map<String, String> sources, Map<String, String> params, File outputFolder) {
		super(sources, params, outputFolder);
	}

	/**
	 * Set goals of this test.
	 *
	 * This test is linear - every next step needs the previous one successfully
	 * completed. At the same time, without the last step, there are no meaningful
	 * results. Therefore this test has only one goal.
	 *
	 * @throws name.hon2a.asm.TestException
	 */
	@Override
	protected void setGoals () throws TestException {
		this.addGoal(DomJavaTest.goalTransformXml, "XML document transformation");
	}

	/**
	 * Execute test body.
	 *
	 * This test tries to parse XML file, compile Java source, run resulting script
	 * describing transformations on XML document, transform document using those
	 * transformations, and save output to file. User-supplied script class needs to
	 * implement DomTransformer interface and use implementation of
	 * DomTransformer::transform() method to execute transformations.
	 *
	 * @throws name.hon2a.asm.TestException
	 */
	@Override
	protected void doTest () throws TestException {


		PrintStream systemErrorStream = System.err;
		PrintStream systemOutputStream = System.out;
		System.setErr(new PrintStream(new NullOutputStream()));
		System.setOut(new PrintStream(new NullOutputStream()));

		this.requireSources(DomJavaTest.sourceJava, DomJavaTest.sourceXml);
		this.requireParams(DomJavaTest.paramDomScript, DomJavaTest.paramOutputFile);

		Document xmlDocument = this.loadXmlFile(this.getSourceFile(DomJavaTest.sourceXml));

		File javaSourcesFolder = this.getSourceFile(DomJavaTest.sourceJava);

		this.compileJavaSources(javaSourcesFolder);

		Map<Class, Object> transformArgs = new HashMap<>();
		transformArgs.put(Document.class, xmlDocument);


        // Here, the user's source code is run
		this.runJavaSource(javaSourcesFolder, this.getParam(DomJavaTest.paramDomScript),
				domScriptMainMethod, transformArgs);


		ByteArrayOutputStream byteArrayOutputStream = new ByteArrayOutputStream();
		DOMSource source = new DOMSource(xmlDocument);
		StreamResult result = new StreamResult(byteArrayOutputStream);
		Transformer transformer;
		try {
			transformer = TransformerFactory.newInstance().newTransformer();
		} catch (TransformerConfigurationException e) {
			throw new TestCodeException("XML transformer cannot be initialized", e);
		}
		try {
			transformer.transform(source, result);
		} catch (Exception e) {
			throw new TestDataException("Document cannot be transformed by provided transformations", e);
		}


		System.setOut(systemOutputStream);
		System.setErr(systemErrorStream);

		this.saveTextFile(this.getParam(DomJavaTest.paramOutputFile),
				new ByteArrayInputStream(byteArrayOutputStream.toByteArray()));

		this.getGoal(DomJavaTest.goalTransformXml).reach();

	}

}
