package name.hon2a.asm;

/**
 * %Error wrapper used by @link Test tests @endlink to specify details of goal
 * or test failure.
 *
 * Adds possibility to save filename and line number of error point of origin.
 *
 * @author %hon2a
 */
public class TestError extends Error {

	private String sourcePath = null; ///< path of file where error occurred
	private int lineNumber = 0; ///< line number on which error occurred

	/**
	 * Default constructor.
	 * 
	 * @param message error message
	 */
	public TestError (String message) {
		super(message);
	}

	/**
	 * Constructor for cases when file of error origin is known.
	 *
	 * @param message error message
	 * @param path path of file where error occurred
	 */
	public TestError (String message, String path) {
		super(message);
		this.sourcePath = path;
	}

	/**
	 * Constructor for cases when file and line number of error origin are known.
	 *
	 * @param message error message
	 * @param path path of file where error occurred
	 * @param lineNumber line number on which error occurred
	 */
	public TestError (String message, String path, int lineNumber) {
		super(message);
		this.sourcePath = path;
		this.lineNumber = lineNumber;
	}

	/**
	 * Return all error info in single human-readable string.
	 *
	 * @return Human-readable error message.
	 */
	@Override
	public String toString () {
		StringBuilder builder = new StringBuilder(this.message);
		if (this.sourcePath != null) {
			builder.append(" (")
				.append(this.sourcePath);
			if (this.lineNumber > 0) {
				builder.append(" at line ")
					.append(this.lineNumber);
			}
			builder.append(")");
		}
		return builder.toString();
	}
}
