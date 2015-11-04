/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package name.hon2a.asmp.xquery;

import name.hon2a.asm.Test;
import name.hon2a.asm.TestException;
import name.hon2a.asm.Utils;
import net.sf.saxon.Configuration;
import net.sf.saxon.query.DynamicQueryContext;
import net.sf.saxon.query.QueryResult;
import net.sf.saxon.query.StaticQueryContext;
import net.sf.saxon.query.XQueryExpression;
import net.sf.saxon.trans.XPathException;
import org.w3c.dom.Document;

import javax.xml.transform.OutputKeys;
import javax.xml.transform.dom.DOMSource;
import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.OutputStream;
import java.util.ArrayList;
import java.util.List;
import java.util.Map;
import java.util.Properties;
import java.util.regex.Pattern;

/**
 *
 * @author hon2a
 */
public class XqueryTest extends Test {

    private final File dataFolder;

	public static final String sourceXml = "xmlDocument"; ///< source ID of xml document
	public static final String sourceXqueryMask = "xqueryMaskLegacy"; ///< ID of xquery source mask (before 2015)
    public static final String sourceXqueryMaskLegacy = "xqueryMask"; ///< ID of xquery source mask

	public static final String paramQueryCountMin = "queryCountMin"; ///< param ID of min. query count param
	public static final String paramOutputXmlMask = "outputXmlMask"; ///< param ID of output xml path mask

	private static final String goalQueryCount = "queryCount"; ///< goal ID of query count goal
	private static final String goalConstructCoverage = "coveredConstructs"; ///< goal ID of XQuery construct coverage goal
	private static final String goalValidQueries = "validQueries"; ///< goal ID of valid queries goal

	/**
	 * Required source: XqueryTest::sourceXml, XqueryTest::sourceXqueryMask;
	 * required parameters: none.
	 */
	public XqueryTest (Map<String, String> sources, Map<String, String> params, File dataFolder, File outputFolder) {
		super(sources, params, outputFolder);
        this.dataFolder = dataFolder;
	}

	@Override
	protected void setGoals () throws TestException {
		this.addGoal(XqueryTest.goalQueryCount, "Required minimum of XQuery queries was supplied");
		this.addGoal(XqueryTest.goalConstructCoverage, "XQuery expressions contain all required constructs");
		this.addGoal(XqueryTest.goalValidQueries, "XQuery expressions are valid and can be executed on supplied XML");
	}

	@SuppressWarnings("ConstantConditions") // we know the dataFolder exists, so listFiles cannot return null
	@Override
	protected void doTest () throws TestException {
		this.requireSources(XqueryTest.sourceXml);

        File[] files = this.dataFolder.listFiles();
        String maskToUse = XqueryTest.sourceXqueryMask;
        for (File file : files)
        {
            if (file.isDirectory() && file.getName().toUpperCase().equals("XQUERY"))
            {
                maskToUse = XqueryTest.sourceXqueryMaskLegacy;
            }
        }

		String xqueryPathMask = this.getSourcePath(maskToUse);
		String[] queries = this.loadQueries(xqueryPathMask);

		int queryCountMin = Integer.parseInt(this.getParam(XqueryTest.paramQueryCountMin));
		if (queries.length < queryCountMin) {
			String error = (queries.length == 0) ? "No XQuery files found"
					: String.format("Only %d XQuery files found", queries.length);
			this.getGoal(XqueryTest.goalQueryCount).fail(error + String.format(" (%d required)", queryCountMin));
		} else {
			this.getGoal(XqueryTest.goalQueryCount).reach();
		}

		String error = this.checkXqueryConstructCoverage(queries);
		this.getGoal(XqueryTest.goalConstructCoverage).reachOnNoError(error);

		Document xmlDocument = this.loadXmlFile(this.getSourceFile(XqueryTest.sourceXml));
		String baseUri = new File(xqueryPathMask).getParentFile().toURI().toString();
		try {
			error = this.runQueries(queries, baseUri, xmlDocument,
					this.getOutputPath(this.getParam(XqueryTest.paramOutputXmlMask)));
			this.getGoal(XqueryTest.goalValidQueries).reachOnNoError(error);
		} catch (IOException e) {
			throw new TestException("Error while saving XQuery result", e);
		}
	}

	protected String[] loadQueries (String pathMask) throws TestException {
		List<String> ret = new ArrayList<>();

		int i = 1;
		File file = new File(String.format(pathMask, i));
		while (file.canRead())
		{
			ret.add(this.stripXqueryComments(this.loadTextFile(file)));
			file = new File(String.format(pathMask, i));
			i++;
		}

		return ret.toArray(new String[ret.size()]);
	}

	protected String stripXqueryComments (String query) {
		int from, to;
		while (((from = query.indexOf("(:")) != -1) && ((to = query.indexOf(":)", from)) != -1)) {
			query = query.substring(0, from) + query.substring(to + 2);
		}
		return query;
	}

	protected String checkXqueryConstructCoverage (String[] queries) {
		String[][] requirements = {
			{"where.*?(min|max|avg|sum)", "min, max, avg or sum function in 'where' clause"},
			{"every.*?satisfies|some.*?satisfies", "every ... satisfies or some ... satisfies"},
			{"distinct-values", "distinct-values"},
			{"if.*then.*else", "if ... then ... else"}
		};

		List<String> errors = new ArrayList<>();
		loopRequirements: for (String[] req : requirements) {
			Pattern pattern = Pattern.compile(req[0], Pattern.UNICODE_CASE
					| Pattern.CASE_INSENSITIVE | Pattern.MULTILINE | Pattern.DOTALL);

			for (String query : queries) {
				if (pattern.matcher(query).find()) {
					continue loopRequirements;
				}
			}
			errors.add("Pattern '" + req[1] + "' not found in any XQuery file.");
		}
		
		return Utils.join(errors.toArray(new String[errors.size()]), "\n");
	}

	protected String runQueries (String[] queries, String baseUri, Document xmlDocument, String outputPathMask)
			throws IOException {
		final Configuration config = new Configuration();

		final Properties props = new Properties();
		props.setProperty(OutputKeys.OMIT_XML_DECLARATION, "no");
		props.setProperty(OutputKeys.INDENT, "yes");

		final StaticQueryContext sqc = new StaticQueryContext(config);
		sqc.setBaseURI(baseUri);

		for (int i = 0; i < queries.length; ++i) {
			String query = queries[i];
			try {
				final XQueryExpression exp = sqc.compileQuery(query);

				final DynamicQueryContext dynamicContext = new DynamicQueryContext(config);
				dynamicContext.setContextItem(config.buildDocument(new DOMSource(xmlDocument)));

				OutputStream out = null;
				try {
					out = new FileOutputStream(String.format(outputPathMask, i + 1));
					QueryResult.serializeSequence(exp.iterator(dynamicContext), config, out, props);
				} finally {
					if (out != null) {
						out.close();
					}
				}
			} catch (XPathException e) {
				return "Error in XQuery expression no." + (i + 1) + ": " + e.getMessage();
			}
		}
		
		return null;
	}
}
