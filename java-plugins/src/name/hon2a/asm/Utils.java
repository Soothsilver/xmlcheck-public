package name.hon2a.asm;

import java.io.*;
import java.nio.file.FileVisitResult;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.SimpleFileVisitor;
import java.nio.file.attribute.BasicFileAttributes;
import java.util.*;
import java.util.regex.Matcher;
import java.util.regex.Pattern;
import java.util.zip.ZipEntry;
import java.util.zip.ZipInputStream;
import java.util.zip.ZipOutputStream;

/**
 * Wrapper module for various utility functions.
 * This file is duplicated by the FilesystemUtils.java file in the similarity module.
 *
 */
public class Utils {

	/// OS-specific line separator
	public static final String EOL_STRING = System.getProperty("line.separator");
	private static final String INDENT_STRING = "   "; ///< default indentation string

	private static final int BUFFER_SIZE = 2048; ///< default buffer size

    /**
     * Copies the contents of the source directory into the second directory.
     * Code by Archimedes Trajano at http://stackoverflow.com/a/10068306/1580088
	 * Slightly modified by Petr Hudecek in order to make it pass IntelliJ IDEA static code analysis
     * @param sourceDirectory directory to copy contents from
     * @param copyInto files and directories from the first directory will be copied into this one
     */
    public static void copyDirectory(File sourceDirectory, File copyInto) throws IOException {
        final Path targetPath = copyInto.toPath();
        final Path sourcePath = sourceDirectory.toPath();
        Files.walkFileTree(sourcePath, new SimpleFileVisitor<Path>() {
            @Override
            public FileVisitResult preVisitDirectory(final Path dir,
                                                     final BasicFileAttributes attributes) throws IOException {
                Files.createDirectories(targetPath.resolve(sourcePath
                        .relativize(dir)));
                return FileVisitResult.CONTINUE;
            }

            @Override
            public FileVisitResult visitFile(final Path file,
                                             final BasicFileAttributes attributes) throws IOException {
                Files.copy(file,
                        targetPath.resolve(sourcePath.relativize(file)));
                return FileVisitResult.CONTINUE;
            }
        });
    }

	/**
	 * Create temporary file with given extension.
	 *
	 * @param extension file extension
	 * @return File descriptor of created file.
	 * @throws java.io.IOException if file cannot be created
	 */
	public static File createTempFile (String extension) throws IOException {
		final File tempFile;
		tempFile = File.createTempFile("asmTempFile_" + Long.toString(System.nanoTime()),
				((extension == null) ? "" : ("." + extension)));

		if(!(tempFile.delete()))
		{
			throw new IOException("Could not delete temp file: " + tempFile.getName());
		}
		if(!(tempFile.createNewFile()))
		{
			 throw new IOException("Could not create temp directory: " + tempFile.getName());
		}

		return tempFile;
	}

	/**
	 * Create temporary file.
	 *
	 * @return File descriptor of created file.
	 * @throws java.io.IOException if file cannot be created
	 */
	public static File createTempFile () throws IOException {
		return createTempFile(null);
	}

	/**
	 * Create temporary folder.
	 *
	 * @return File descriptor of created folder.
	 * @throws java.io.IOException if folder cannot be created
	 */
	public static File createTempDirectory () throws IOException {
		final File tempDir;
		tempDir = File.createTempFile("asmTempFolder_", Long.toString(System.nanoTime()));

		if(!(tempDir.delete()))
		{
			throw new IOException("Could not delete temp file: " + tempDir.getName());
		}
		if(!(tempDir.mkdir()))
		{
			 throw new IOException("Could not create temp directory: " + tempDir.getName());
		}

		return tempDir;
	}

	/**
	 * Load text file to string.
	 *
	 * @param source file descriptor of source file
	 * @return Text contents of source file in a string.
	 * @throws java.io.FileNotFoundException
	 * @throws java.io.IOException
	 */
	public static String loadTextFile (File source) throws IOException {
		StringBuilder textContent = new StringBuilder(BUFFER_SIZE);
		FileInputStream fi = new FileInputStream(source);

		UnicodeBOMInputStream inputStream = new UnicodeBOMInputStream(fi).skipBOM();

		InputStreamReader ir;
		try {
			ir = new InputStreamReader(inputStream, "UTF-8");
		} catch (Exception e) {
			ir = new InputStreamReader(inputStream);
		}


		char[] buf = new char[BUFFER_SIZE];
		int numRead;

		try (Reader reader = new BufferedReader(ir, BUFFER_SIZE)) {
			while ((numRead = reader.read(buf)) != -1) {
				textContent.append(String.valueOf(buf, 0, numRead));
			}
		}

		return textContent.toString();
	}

	/**
	 * Recode file to default system encoding.
	 *
	 * @param source file descriptor of source file
	 * @throws java.io.FileNotFoundException
	 * @throws java.io.IOException
	 */
	public static void recodeFileToDefaultEncoding (File source)
			throws IOException {
		String contents = Utils.loadTextFile(source);
		try (Writer output = new BufferedWriter(new FileWriter(source))) {
			output.write(contents);
		}
	}

	/**
	 * Save data from input stream to text file.
	 *
	 * @param destination destination file
	 * @param is input stream
	 * @param charsetName name of charset which should be used (default if null)
	 * @throws java.io.IOException
	 */
	@SuppressWarnings("ResultOfMethodCallIgnored")
	public static void saveTextFile (File destination, InputStream is, String charsetName) throws IOException {
		BufferedWriter writer = null;
		BufferedReader reader = null;
		destination.createNewFile();
		try {
			writer = new BufferedWriter(new FileWriter(destination), BUFFER_SIZE);
			reader = (charsetName == null)
				? new BufferedReader(new InputStreamReader(is), BUFFER_SIZE)
				: new BufferedReader(new InputStreamReader(is, charsetName), BUFFER_SIZE);
			int count;
			char[] charBuffer = new char[BUFFER_SIZE];
			while ((count = reader.read(charBuffer, 0, BUFFER_SIZE)) != -1) {
				writer.write(charBuffer, 0, count);
			}
		} finally {
			if (writer != null) {
				writer.close();
			}
			if (reader != null) {
				reader.close();
			}
		}
	}

	/**
	 * Save data from input stream to binary file.
	 *
	 * @param destination destination file
	 * @param contents input stream
	 * @throws java.io.IOException
	 */
	@SuppressWarnings("ResultOfMethodCallIgnored")
	public static void saveBinaryFile (File destination, InputStream contents) throws IOException {
		BufferedOutputStream os = null;
		BufferedInputStream is = null;
		destination.createNewFile();
		try {
			is = new BufferedInputStream(contents, BUFFER_SIZE);
			os = new BufferedOutputStream(new FileOutputStream(destination), BUFFER_SIZE);
			int count;
			byte[] byteBuffer = new byte[BUFFER_SIZE];
			while ((count = is.read(byteBuffer, 0, BUFFER_SIZE)) != -1) {
				os.write(byteBuffer, 0, count);
			}
		} finally {
			if (is != null) {
				is.close();
			}
			if (os != null) {
				os.close();
			}
		}
	}

	/**
	 * Indent text.
	 *
	 * @param text text to be indented
	 * @param count indentation depth
	 * @param filler indentation string
	 * @return Indented text.
	 */
	private static String indent(String text, int count, String filler) {
		if ((text == null) || (count < 0) || (filler == null)) {
			return null;
		}

		StringBuilder fillerBuilder = new StringBuilder();
		for (int i = 0; i < count; ++i) {
			fillerBuilder.append(filler);
		}
		filler = fillerBuilder.toString();

		StringWriter buffer = new StringWriter();
		BufferedWriter writer = new BufferedWriter(buffer);
		BufferedReader reader = new BufferedReader(new StringReader(text));
		String line;
		try {
			while ((line = reader.readLine()) != null) {
				writer.write(filler + line);
				writer.newLine();
			}
			reader.close();
			writer.close();
		} catch (IOException ignored) {
		}

		return buffer.toString();
	}

	/**
	 * Indent text with default indentation string.
	 *
	 * @param text text to be indented
	 * @param count indentation depth
	 * @return Indented text.
	 */
	private static String indent(String text, int count) {
		return indent(text, count, INDENT_STRING);
	}

	/**
	 * Indent text by one with default indentation string.
	 *
	 * @param text text to be indented
	 * @return Indented text.
	 */
	public static String indent (String text) {
		return indent(text, 1);
	}

	/**
	 * Format error so that error details are indented and appended on next line
	 * after error message.
	 *
	 * @param message error message
	 * @param details error details
	 * @return Formatted error message.
	 */
	public static String indentError (String message, String details) {
		return (message + EOL_STRING + indent(details));
	}

	/**
	 * Add folder contents to given zip output stream.
	 *
	 * @param sourceFolder source folder
	 * @param zos output stream
	 * @param pathBase path prefix for added entries
	 * @throws java.io.IOException
	 */
	private static void zipDirectory (File sourceFolder, ZipOutputStream zos, String pathBase) throws IOException {
		String[] folderContents = sourceFolder.list();
		byte[] byteBuffer = new byte[BUFFER_SIZE];
		int count;
		for (String fileName : folderContents) {
			File file = new File(sourceFolder, fileName);
			StringBuilder entryBuilder = new StringBuilder(pathBase);
			if (!pathBase.equals("")) {
				entryBuilder.append(File.separator);
			}
			entryBuilder.append(file.getName());
			if (file.isDirectory()) {
				zipDirectory(sourceFolder, zos, entryBuilder.toString());
			} else {
				BufferedInputStream is = null;
				try {
					is = new BufferedInputStream(new FileInputStream(file), BUFFER_SIZE);
					zos.putNextEntry(new ZipEntry(entryBuilder.toString()));
					while ((count = is.read(byteBuffer)) != -1) {
						zos.write(byteBuffer, 0, count);
					}
				} finally {
					if (is != null) {
						is.close();
					}
				}
			}
		}
	}

	/**
	 * Pack folder contents into single ZIP archive.
	 *
	 * @param sourceFolder source folder
	 * @param archive destination file
	 * @throws java.io.FileNotFoundException
	 * @throws java.io.IOException
	 */
	public static void zip (File sourceFolder, File archive)
			  throws IOException {
		ZipOutputStream zos = null;
		try {
			zos = new ZipOutputStream(new FileOutputStream(archive));
			zipDirectory(sourceFolder, zos, "");
		} finally {
			if (zos != null) {
				zos.close();
			}
		}
	}

	/**
	 * Unpack contents of ZIP archive to given folder.
	 *
	 * @param archive ZIP archive
	 * @param destinationFolder destination folder
	 * @throws java.io.FileNotFoundException
	 * @throws java.io.IOException
	 */
	@SuppressWarnings("ResultOfMethodCallIgnored")
	public static void unzip (File archive, File destinationFolder)
			  throws IOException  {
		ZipInputStream zis = new ZipInputStream(new BufferedInputStream(new FileInputStream(archive)));
		ZipEntry entry;
		while ((entry = zis.getNextEntry()) != null) {
			File destinationFile = new File(destinationFolder, entry.getName());
			if (entry.isDirectory()) {
				if (destinationFile.exists() && !destinationFile.isDirectory()) {
					destinationFile.delete();
				}
				destinationFile.mkdirs();
			} else {
				if (!destinationFile.getParentFile().isDirectory()) {
					destinationFile.getParentFile().mkdirs();
				}

				int count;
				byte[] data = new byte[BUFFER_SIZE];
				BufferedOutputStream destinationStream = null;
				try {
					destinationStream = new BufferedOutputStream(new FileOutputStream(destinationFile), BUFFER_SIZE);
					while ((count = zis.read(data, 0, BUFFER_SIZE)) != -1) {
						destinationStream.write(data, 0, count);
					}
					destinationStream.flush();
				} finally {
					if (destinationStream != null) {
						destinationStream.close();
					}
				}
			}
		}
		zis.close();
	}

	/**
	 * Remove folder and its contents.
	 *
	 * @param directory folder to be removed
	 * @return True if removal was successful, false otherwise.
	 */
	public static boolean removeDirectoryAndContents (File directory) {
		if (directory == null) {
			return false;
		}
		if (!directory.isDirectory()) {
			return false;
		}

		String[] contents = directory.list();
		boolean done = true;
		if (contents != null) {
			for (String fileName : contents) {
				File entry = new File(directory, fileName);
				if (entry.isDirectory()) {
					done = removeDirectoryAndContents(entry);
				} else {
					done = entry.delete();
				}
			}
		}
		if (done) {
			done = directory.delete();
		}
		return done;
	}

	/**
	 * Remove file or folder.
	 *
	 * @param file file descriptor
	 * @return True if removal was successful, false otherwise.
	 */
	public static boolean removeAnyFile (File file) {
		if (file.isDirectory()) {
			return removeDirectoryAndContents(file);
		} else {
			if (!file.exists()) {
				return false;
			}
			return file.delete();
		}
	}

	/**
	 * Functions like toString(), except it returns an empty string rather than the string "null" when the given object is null.
	 *
	 * @param object the object on which to call toString()
	 * @return object.toString() or "" if the object was null
	 */
	private static String toString (Object object) {
		if (object == null) {
			return "";
		}
		return object.toString();
	}

	/**
	 * Turn collection to single string with elements delimited by custom string.
	 *
	 * @param collection collection to be joined into a string by the delimiter
	 * @param delimiter custom delimiter, or null to join by an empty string
	 * @return String consisting of collection elements delimited by custom string.
	 */
	public static String join (AbstractCollection collection, String delimiter) {
		if (collection == null) {
			return "";
		}
		return join(collection.toArray(), delimiter);
	}

	/**
	 * Turn array to single string with elements delimited by custom string.
	 *
	 * @param array array to be to be joined into a string by the delimiter
	 * @param delimiter custom delimiter, or null to join by an empty string
	 * @return String consisting of array elements delimited by custom string.
	 */
	public static String join (Object[] array, String delimiter) {
		if (array.length == 0) {
			return "";
		} else if (array.length == 1) {
			return toString(array[0]);
		}

		StringBuilder builder = new StringBuilder(toString(array[0]));
		for (int i = 1; i < array.length; ++i) {
			builder.append(toString(delimiter))
				.append(toString(array[i]));
		}
		return builder.toString();
	}

	/**
	 * Turn array to single string.
	 *
	 * @param array array to be joined into a string, one item after another
	 * @return String consisting of array elements joined together.
	 */
	public static String join (Object [] array) {
		return join(array, null);
	}

	/**
	 * Split string as if it was command-line arguments string.
	 *
	 * First takes out all quoted substrings and splits the rest by spaces. Quoted
	 * substrings are separate entries even if not surrounded by spaces.
	 *
	 * @param string string to be split
	 * @return Array of string arguments.
	 */
	public static String[] splitArguments (String string) {
		StringBuilder stringWorker = new StringBuilder(string);
		Matcher matcher = Pattern.compile("(^|[^\\\\])\"([^\"\\\\]+(\\\\\"[^\\\\])?)*\"")
				.matcher(stringWorker.toString());
		List<String> strings = new ArrayList<>();
		int pos = 0;
		while (matcher.find()) {
			int start = matcher.start();
			if (start > pos) {
				addSplitPartsToArray(stringWorker.substring(pos, start + 1), strings);
			}
			pos = matcher.end();

			String group = matcher.group();
			if (group.charAt(0) == '"') {
				strings.add(group.substring(1, group.length() - 1));
			} else {
				strings.add(group.substring(2, group.length() - 1));
			}
		}
		if (stringWorker.length() > pos) {
			addSplitPartsToArray(stringWorker.substring(pos, stringWorker.length()), strings);
		}
		return strings.toArray(new String[strings.size()]);
	}

	/**
	 * Split string by spaces and add non-empty parts to list.
	 * 
	 * @param string string to be split
	 * @param list list to receive parts
	 */
	private static void addSplitPartsToArray (String string, List<String> list) {
		String[] parts = string.split(" ");
		for (String part : parts) {
			if (!part.equals("")) {
				list.add(part);
			}
		}
	}

	/**
	 * Create associative array of strings using odd strings as keys and even as values.
	 *
	 * @param entries keys and values combined
	 * @return Associative array with string keys and values.
	 */
	public static Map<String, String> createStringMap (String ... entries) {
		Map<String, String> map = new HashMap<>(entries.length / 2);
		for (int i = 1; i < entries.length; i += 2) {
			map.put(entries[i - 1], entries[i]);
		}
		return map;
	}

	/**
	 * Create associative array of file descriptors using odd strings as keys and
	 * even as paths.
	 *
	 * @param entries keys and file paths combined
	 * @return Associative array with string keys and file descriptor values.
	 */
	public static Map<String, File> createFileMap (String ... entries) {
		Map<String, File> map = new HashMap<>(entries.length / 2);
		for (int i = 1; i < entries.length; i += 2) {
			map.put(entries[i - 1], new File(entries[i]));
		}
		return map;
	}

	/**
	 * Retrieve stack trace of given exception as string.
	 *
	 * @param e exception
	 * @return Stack trace.
	 */
	public static String getStackTrace (Throwable e) {
		StringWriter bufferWriter = new StringWriter();
		e.printStackTrace(new PrintWriter(bufferWriter));
		return bufferWriter.toString();
	}

	/**
	 * Create simple message from exception using its message (or its class name
	 * in case of no message).
	 *
	 * @param e exception
	 * @return Exception message if present, class name otherwise.
	 */
	private static String createSimpleMessage (Throwable e) {
		String message = e.getMessage();
		if (message == null) {
			message = e.getClass().getSimpleName();
		}
		return message;
	}

	/**
	 * Create detailed message from exception using top of its stack trace to add
	 * additional info.
	 *
	 * @param e exception
	 * @return Detailed message with exception class and possibly file and line number.
	 */
	private static String createDetailedMessage (Throwable e) {
		StringBuilder messageBuilder = new StringBuilder();
		StackTraceElement[] trace = e.getStackTrace();
		messageBuilder.append(e.getClass().getSimpleName());
		if (trace.length != 0) {
			messageBuilder.append(" @ ")
					.append(trace[0].getFileName());
			int lineNumber = trace[0].getLineNumber();
			if (lineNumber > 0) {
				messageBuilder.append(":")
						.append(lineNumber);
			}
		}
		String simpleMessage = e.getMessage();
		if (simpleMessage != null) {
			messageBuilder.insert(0, " (")
					.insert(0, simpleMessage)
					.append(")");
		}
		return messageBuilder.toString();
	}

	/**
	 * Create human-readable message from exception stack.
	 *
	 * Function goes through exception stack using Throwable::getCause() and prints
	 * short info about each exception in the stack.
	 *
	 * @param e exception (top of the stack)
	 * @param detailed set to true to get detailed report instead of just messages
	 * @return Human-readable exception report.
	 */
	public static String getMessageTrace (Throwable e, boolean detailed) {
		StringBuilder traceBuilder = new StringBuilder();
		while (e != null) {
			String message = detailed ? createDetailedMessage(e) : createSimpleMessage(e);
			traceBuilder.append(message)
				.append(EOL_STRING);
			e = e.getCause();
		}
		return traceBuilder.toString();
	}

	/**
	 * Create simple human-readable message from exception stack.
	 *
	 * @param e exception (top of the stack)
	 * @return Human-readable exception report.
	 */
	public static String getMessageTrace (Throwable e) {
		return getMessageTrace(e, false);
	}

	public static String escapeXml (String str) {
		str = str.replace("&", "&amp;");
		str = str.replace("<", "&lt;");
		str = str.replace(">", "&gt;");
		return str;
	}
}
