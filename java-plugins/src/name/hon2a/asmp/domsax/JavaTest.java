package name.hon2a.asmp.domsax;

import name.hon2a.asm.Test;
import name.hon2a.asm.TestException;
import name.hon2a.asm.Utils;

import java.io.*;
import java.lang.reflect.Method;
import java.net.URL;
import java.net.URLClassLoader;
import java.util.HashMap;
import java.util.Map;

/**
 * Abstract test providing support for compiling and running external java sources.
 *
 * @author %hon2a
 */
public abstract class JavaTest extends Test {

	/**
	 * Initializes a new instance of JavaTest.
	 * @param sources Locations of source files.
	 * @param params Values of parameters.
	 * @param outputFolder Where to put output files.
	 */
	protected JavaTest(Map<String, String> sources, Map<String, String> params, File outputFolder) {
		super(sources, params, outputFolder);
	}

	/**
	 * Initializes a new instance of JavaTest without output.
	 * @param sources Locations of source files.
	 * @param params Values of parameters.
	 */
	protected JavaTest(Map<String, String> sources, Map<String, String> params) {
		super(sources, params, null);
	}

	/**
	 * Initializes a new instance of JavaTest without parameters.
	 * @param sources Locations of source files.
	 * @param outputFolder Where to put output files.
	 */
	protected JavaTest(Map<String, String> sources, File outputFolder) {
		super(sources, null, outputFolder);
	}

	/**
	 * Initializes a new instance of JavaTest without parameters or output.
	 * @param sources Locations of source files.
	 */
	protected JavaTest(Map<String, String> sources) {
		super(sources, null, null);
	}

	/**
	 * Compile Java source file.
	 *
	 * @param source source file descriptor
	 * @throws name.hon2a.asm.TestException in case source cannot be compiled
	 */
	protected final void compileJavaSource (File source) throws TestException {
		final String[] javacArguments = new String[] { source.getAbsolutePath() };
		OutputStream errorStream = new ByteArrayOutputStream();
		final PrintWriter pw = new PrintWriter(errorStream);

		int errorCode = com.sun.tools.javac.Main.compile(javacArguments, pw);

        pw.flush();
        pw.close();
		if (errorCode != 0) {
			this.triggerError("Source cannot be compiled (error code " + errorCode + ", " + errorStream.toString() + ")", ErrorType.DATA_ERROR);
		}
	}

	/**
	 * Compile all Java sources in given folder and subfolders (recursive).
	 *
	 * @param sourcePath file descriptor of base folder
	 * @throws name.hon2a.asm.TestException in case some source file cannot be compiled
	 */
	protected final void compileJavaSources (File sourcePath) throws TestException {
		File[] subFolders = sourcePath.listFiles(File::isDirectory);
		for (File subFolder : subFolders) {
			this.compileJavaSources(subFolder);
		}

		File[] sourceFiles = sourcePath.listFiles((dir, name) -> name.endsWith(".java"));
		for (File sourceFile : sourceFiles) {
			this.compileJavaSource(sourceFile);
		}
	}

	/**
	 * Loads a Java class from a Java class file
	 * @param classPath Path to the class file.
	 * @param className Name of the class.
	 * @return The loaded Java class.
	 * @throws TestException When any error occurs.
	 */
	protected final Object loadJavaSource (File classPath, String className) throws TestException {
		try {
			URLClassLoader loader = URLClassLoader.newInstance(new URL[] { classPath.toURI().toURL() });
			return loader.loadClass(className).newInstance();
		} catch (Exception e) {
			this.triggerError(Utils.indentError("Cannot load external Java class",
					  Utils.getMessageTrace(e, true)), ErrorType.DATA_ERROR);
			return null;
		}
	}

	/**
	 * Run external class from previously compiled source.
	 *
	 * Class needs to be compiled.
	 *
	 * @param classPath base classpath for class loading
	 * @param className name of class to be loaded
	 * @param methodName name of method to be invoked
	 * @param args arguments passed to run script
	 * @throws name.hon2a.asm.TestException in case script cannot be loaded or throws an exception
	 * @return Return value of the method specified by caller
	 */
	@SuppressWarnings({"ToArrayCallWithZeroLengthArrayArgument", "unchecked"})
	// ToArrayCallWithZeroLengthArrayArgument is suppressed because the performance gains are doubtful, and it would clutter the code
	// unchecked is suppressed because the type of the parameters is unknown (depends on whether DomJavaTest or SaxJavaTest is used)
	protected final Object runJavaSource (File classPath, String className,
			  String methodName, Map<Class, Object> args)
			throws TestException {
		try {
			URLClassLoader loader = URLClassLoader.newInstance(new URL[]{classPath.toURI().toURL()});
			Class userClass = loader.loadClass(className);
			Method userMethod = userClass.getMethod(methodName, args.keySet().toArray(new Class[]{}));
			return userMethod.invoke(userClass.newInstance(), args.values().toArray(new Object[]{}));
		} catch (Exception e) {
			this.triggerError(Utils.indentError("Error while running external Java script",
					  Utils.getMessageTrace(e, true)), ErrorType.DATA_ERROR);
			return null;
		}
	}

	/**
	 * Run external class from previously compiled source without arguments.
	 *
	 * @param classPath base classpath for class loading
	 * @param className name of class to be loaded
	 * @param methodName name of method to be invoked
	 * @throws name.hon2a.asm.TestException in case script cannot be loaded or throws an exception
	 * @return Return value of the method specified by caller
	 */
	protected final Object runJavaSource (File classPath, String className, String methodName)
			  throws TestException {
		return this.runJavaSource(classPath, className, methodName, new HashMap<>());
	}
}
