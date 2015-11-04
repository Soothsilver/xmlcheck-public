<?php

namespace asm\plugin;

/**
 * Test to be used by TesterPlugin.
 *
 * Should be used to contain tests that share the same input or other resources
 * and would have to contain lot of duplicate code if split. Tests that can be
 * separated in separate Test instances should be.
 *
 * Just like Plugin, this class manages test criteria called 'goals'. Goals should
 * be added and updated using this class' methods, not directly.
 *
 * Tests are run using run() method and should implement main test logic inside
 * main() method.
 *
 * Methods with @c [reporting] in their description add descriptive errors to
 * test results in addition to returning simple error-indicating value.
 */
abstract class Test
{
	protected $name;	///< test name
	protected $paths = array();	///< paths of required source files
	protected $params;	///< parameters test was run with
	protected $errors;	///< test errors
	protected $goals;	///< test goals
	
	private $triggeredErrors = array();	///< errors triggered inside error-based test section

	/**
	 * Sets test name to test class name.
	 */
	public function __construct ()
	{
		$this->name = get_class($this);
	}

	/**
	 * Runs plugin with supplied sources & other parameters.
	 * @param array $paths associative array with paths of source files (required
	 *		keys should be declared in test documentation)
	 * @param array $params associative array with test parameters
	 * @return array associative array containing following members:
	 * @arg @a name test name
	 * @arg @a results array with test results (goals)
	 * @arg @a errors array with errors that occurred during test execution
	 * @see main()
	 */
	public final function run ($paths, $params = array())
	{
		$this->errors = $this->goals = array();
		$this->paths = $paths;
		$this->params = $params;
		$this->main();
		return array('name' => $this->name, 'results' => $this->goals, 'errors' => $this->errors);
	}

	/**
	 * Contains main test logic.
	 * Test parameters are located in @ref $params member.
	 */
	abstract protected function main ();

	/**
	 * Checks whether all required test sources are set and readable [reporting].
	 * @param string [...] source names (keys in @c $paths argument supplied to run())
	 * @return bool true if all required sources are accessible
	 */
	protected final function checkSources ()
	{
		foreach (func_get_args() as $source)
		{
			if (!isset($this->paths[$source]))
			{
				$this->addError("Path for source '$source' is not set.");
				return false;
			}
			if (!is_file($this->paths[$source]))
			{
				$this->addError("Cannot find source '$source' at path {$this->paths[$source]}.");
				return false;
			}
		}
		return true;
	}

	/**
	 * Adds error message to test results.
	 * @param string $message error message
	 */
	protected final function addError ($message)
	{
		$this->errors[] = $message;
	}

	/**
	 * Adds new unreached goal with supplied ID, name, and description to test results.
	 * @param string $id unique goal ID (will be used for goal updates)
	 * @param string $name descriptive test name (will be used in test results)
	 * @param string $description detailed test description if test cannot be
	 *		properly described in just one sentence in @c $name
	 */
	protected final function addGoal ($id, $name, $description = null)
	{
		$this->goals[$id] = array(
			'name' => $name,
			'description' => $description,
			'reached' => false,
			'error' => '',
			'line' => null,
			'sourceId' => null,
		);
	}

    public final function reachNewGoal($name, $description)
    {
        $this->goals[] = array(
            'name' => $name,
            'description' => $description,
            'reached' => true,
            'error' => '',
            'line' => null,
            'sourceId' => null
        );
    }


	/**
	 * Adds set of new unreached goals with supplied identification data to test results.
	 * @param array $goals keys will be used as goal IDs, values can either be
	 *		simple strings (will be used as goal names) or arrays {name, description}
	 */
	protected final function addGoals (array $goals)
	{
		foreach ($goals as $goalId => $props)
		{
			list($name, $description) = is_array($props) ? $props : array($props, '');
			$this->addGoal($goalId, $name, $description);
		}
	}

	/**
	 * Mark goal with supplied ID as reached.
	 * @param string $id goal ID
	 * @return bool true
	 */
	protected final function reachGoal ($id)
	{
		$this->goals[$id]['reached'] = true;
		return true;
	}

	/**
	 * Mark goal with supplied ID as failed and add failure details.
	 * @param string $id goal ID
	 * @param string $message failure details
	 * @param int $line line in source which caused goal failure (must be used
	 *		together with @c $sourceId)
	 * @param string $sourceId ID of source which caused goal failure
	 * @return bool false
	 */
	protected final function failGoal ($id, $message, $line = null, $sourceId = null)
	{
		$this->goals[$id]['error'] = $message;
		$this->goals[$id]['line'] = $line;
		$this->goals[$id]['sourceId'] = $sourceId;
		return false;
	}

	/**
	 * Mark set of goals as failed with same failure info.
	 * @param array $ids goal IDs
	 * @param string $message failure details
	 * @param int $line line in source which caused goal failure (must be used
	 *		together with @c $sourceId)
	 * @param string $sourceId ID of source which caused goal failure
	 * @return bool false
	 */
	protected final function failGoals (array $ids, $message, $line = null, $sourceId = null)
	{
		foreach ($ids as $id)
		{
			$this->failGoal($id, $message, $line, $sourceId);
		}
		return false;
	}

	/**
	 * Mark goal as reached or failed with supplied failure info if supplied
	 * condition is true.
	 * @param bool $condition goal will be marked as reached if set to true
	 * @param string $id goal ID
	 * @param string $message failure info (used only if @c $condition is true)
	 * @param int $line line in source which caused goal failure (must be used
	 *		together with @c $sourceId)
	 * @param string $sourceId ID of source which caused goal failure
	 * @return bool value of @c $condition
	 */
	protected final function reachGoalOnCondition($condition, $id, $message, $line = null, $sourceId = null)
	{
		if ($condition)
		{
			return $this->reachGoal($id);
		}
		else
		{
			return $this->failGoal($id, $message, $line, $sourceId);
		}
	}

	/**
	 * Gets test goal IDs & names.
	 * @return array goal IDs as keys with their respective names as values
	 */
	public final function getGoals ()
	{
        $ret = array();
		foreach ($this->goals as $goalId => $data)
		{
			$ret[$goalId] = $data['name'];
		}
		return $ret;
	}

	/**
	 * Stores errors triggered inside error-based sections.
	 * Should not be used directly (it's public only to be a valid callback).
	 * @param int $errno one of predefined @c E_* constants
	 * @param string $errstr error message
	 * @param string $errfile file in which error was triggered
	 * @param int $errline line on which error was triggered
	 */
	public function triggeredErrorHandler (
		/** @noinspection PhpUnusedParameterInspection */
		$errno,
		$errstr,
		$errfile = null,
		$errline = null)
	{
		// $errno is not used currently and is discarded.
		// It does not provide any useful information anyway.
		$this->triggeredErrors[] = array(
			'error' => $errstr,
			'file' => $errfile,
			'line' => $errline,
		);
	}

	/**
	 * Starts error-based test section.
	 * Inside such section of code all catchable errors are suppressed and stored
	 * to be available for retrieval later. This is useful when you with to use
	 * errors triggered by PHP functions as failure info.
	 * @see reachGoalErrorBased()
	 * @see endErrorBasedSection()
	 * @see getTriggeredErrors()
	 */
	protected final function startErrorBasedSection()
	{
		set_error_handler(array($this, 'triggeredErrorHandler'));
	}

	/**
	 * Ends error-based test section.
	 * @see startErrorBasedSection()
	 */
	protected final function endErrorBasedSection()
	{
		restore_error_handler();
	}

	/**
	 * Gets errors caught inside last/current error-based section.
	 * Clears error store after retrieving errors if @a $clear is set to true (default).
	 * @param bool $clear false to keep errors
	 * @return array errors caught and stored during error-based test sections
	 *		since last call of this method or reachGoalErrorBased()
	 * @see startErrorBasedSection()
	 * @see reachGoalErrorBased()
	 */
	protected final function getTriggeredErrors ($clear = true)
	{
		$ret = $this->triggeredErrors;
        if ($clear)
        {
		    $this->triggeredErrors = array();
        }
		return $ret;
	}

	/**
	 * Removes error with supplied index from triggered errors.
	 * @param int $index index in array returned by getTriggeredErrors()
	 */
	protected final function removeTriggeredError ($index)
	{
        unset($this->triggeredErrors[$index]);
	}

	/**
	 * Marks goal as reached/failed based on errors caught during last error-based
	 * test section.
	 * Sample use (in descendant):
	 * @code
	 * // during test setup:
	 * $this->addGoal('foo', 'Input must be blah, blah, blah...');
	 * // ...
	 * // during test execution:
	 * $this->startErrorBasedSection();
	 *	// execute code that triggers errors just when the input 'bar' does not
	 * // pass test criteria (let's call this hypothetical function 'fn'):
	 * fn($this->paths['bar']);
	 * $this->endErrorBasedSection();
	 * $this->reachGoalErrorBased('foo', 'bar');
	 * @endcode
	 * If fn() triggered no errors, than the 'foo' goal is marked as reached,
	 * otherwise it is marked as failed with first triggered error appended as
	 * its failure info.
	 * @param string $id goal ID
	 * @param string $sourceId ID of source file that was being tested
	 * @return bool true if goal was reached
	 */
	protected final function reachGoalErrorBased ($id, $sourceId)
	{
		$errors = $this->getTriggeredErrors();
        $errors = array_values($errors); // This is because some triggered errors may have been removed and their indices too
		if (!empty($errors))
		{
			$error = $errors[0];
			if (realpath($error['file']) != realpath($this->paths[$sourceId]))
				$error['line'] = null;
			return $this->failGoal($id, htmlentities($error['error']), $error['line'], $sourceId);
		}
		else
		{
			return $this->reachGoal($id);
		}
	}
    protected final function reachGoalIfNoErrorThenClearErrors ($id)
    {
        $errors = $this->getTriggeredErrors(true);
        $errors = array_values($errors); // This is because some triggered errors may have been removed and their indices too
        if (!empty($errors))
        {
            $error = $errors[0];
            return $this->failGoal($id, htmlentities($error['error']));
        }
        else
        {
            return $this->reachGoal($id);
        }
    }

	/**
	 * Mark goal as reached/failed based on CountedRequirements instance.
	 * @param CountedRequirements $requirements requirements for goal to be reached
	 * @param string $goalId goal ID
	 * @param string $failString failure info template (must contain placeholders
	 *		for 'number of subjects found' (%d) 'subjects' (%s), and 'minimum required
	 *		number' (%d) respectively)
	 * @return bool true if goal was reached
	 */
	protected final function resolveCountedRequirements (CountedRequirements $requirements,
		$goalId, $failString = 'Documents contain %d %s (minimum of %d required)')
	{
		if (!$requirements->resolve($description, $occurred, $required))
		{
			return $this->failGoal($goalId, sprintf($failString, $occurred, $description, $required));
		}
		return $this->reachGoal($goalId);
	}

    public final function hasErrors()
    {
        return count($this->errors) > 0;
    }
}

