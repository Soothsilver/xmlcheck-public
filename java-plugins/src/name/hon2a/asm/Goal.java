package name.hon2a.asm;

/**
 * Wrapper class for Test goals.
 *
 * Represents single goal that can be reached. Stores @link name goal description @endlink,
 * @ref reached flag and possibly @ref error in case of failure.
 * 
 * @author %hon2a
 */
public class Goal {
	/**
	 * Human-readable goal description
	 */
	protected final String name;
	/**
	 * Reached flag (true if goal is reached)
	 */
	protected boolean reached = false;
	/**
	 * Error (needs to be set if goal is failed)
	 */
	protected TestError error = null; 

	/**
	 * Sole constructor that sets goal description.
	 * 
	 * @param name goal description
	 */
	public Goal (String name) {
		this.name = name;
	}

	/**
	 * Set Goal::reached flag.
	 */
	public void reach () {
		this.reached = true;
	}

	/**
	 * Flag goal as failed and set detailed error.
	 *
	 * @param message error message
	 * @param sourcePath path to error origin
	 * @param lineNumber line number or error origin
	 */
	protected void fail(String message, String sourcePath, int lineNumber) {
		this.reached = false;
		this.error = new TestError(message, sourcePath, lineNumber);
	}

	/**
	 * Flag goal as failed and set simple error.
	 *
	 * @param message error message
	 */
	public void fail (String message) {
		this.fail(message, null, 0);
	}

	/**
	 * Reach or fail goal based on given condition.
	 *
	 * Calls reach() if condition is true, otherwise calls fail().
	 *
	 * @param condition condition on which goal is reached
	 * @param errorMessage error message in case of failure
	 * @param sourcePath path to error origin
	 * @param lineNumber line number of error origin
	 */
	protected void reachOnCondition(boolean condition, String errorMessage, String sourcePath, int lineNumber) {
		if (condition) {
			this.reach();
		} else {
			this.fail(errorMessage, sourcePath, lineNumber);
		}
	}

	/**
	 * Reach or fail goal based on given condition.
	 *
	 * Calls reach() if condition is true, otherwise calls fail(). Used in case
	 * of unknown error point of origin.
	 *
	 * @param condition condition on which goal is reached
	 * @param errorMessage error message in case of failure
	 */
	protected void reachOnCondition(boolean condition, String errorMessage) {
		this.reachOnCondition(condition, errorMessage, null, 0);
	}

	/**
	 * Reach goal if error is null.
	 *
	 * @param error error message. Goal is reached if error message is null.
	 */
	public void reachOnNoError (String error) {
		this.reachOnCondition(((error == null) || (error.isEmpty())), error);
	}
}
