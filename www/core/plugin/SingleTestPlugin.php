<?php

namespace asm\plugin;
use asm\utils\StringUtils;

/**
 * Plugin containing single Test and using its goals as criteria.
 *
 * Implementing main testing logic in Test is useful, because the test can be
 * reused in other plugins if ever needed. This class implements seamless
 * integration with Test, so that the splitting of plugin and test logic doesn't
 * mean any additional work for the developer.
 *
 * @see TesterPlugin
 */
abstract class SingleTestPlugin extends Plugin {

    /**
     * @var Test
     */
    private $test = null;	///< contained test
	private $sources;			///< test sources
	private $params;			///< test parameters
	private $results;			///< test results

	/**
	 * Sets test to be managed by this plugin.
	 * @param Test $test
	 * @param array $sources source IDs as keys, paths relative to input folder
	 *		as values (will be replaced with absolute before being passed to test)
	 * @param array $params required parameters depend on used test
	 */
	protected final function setTest (Test $test, array $sources, array $params = array())
	{
		$this->test = $test;
		$this->sources = $this->makeFullSources($sources);
		$this->params = $params;
	}

	/**
	 * Executes contained test and turns test results to plugin criteria (main
	 * plugin logic).
	 * @throws PluginException in case no test is set
	 * @see setTest()
	 */
	protected final function execute ()
	{
		if ($this->test !== null)
		{
			$this->results = $this->test->run($this->sources, $this->params);
			$this->createCriteriaFromTest();
		}
		else
		{
			throw new PluginException('No test is set to be executed.');
		}
	}

	/**
	 * Turns test results to plugin criteria or error.
	 * Goal names will be used as criterion names. Criteria will be passed with
	 * 100% fulfillment if goal was reached or failed with goal error as failure
	 * info otherwise.
	 *	@throws PluginException in case test finished with error(s), so that the
	 *		error is returned as plugin error
	 */
	private function createCriteriaFromTest ()
	{
		if (count($this->results['errors']))
		{
			throw new PluginException("Failure. "
					. StringUtils::indent(implode("\n", $this->results['errors'])));
		}

		foreach ($this->test->getGoals() as $goalId => $goalName)
		{
			$this->addCriterion($goalName);
			$results = $this->results['results'][$goalId];
			$passed = $results['reached'];
			$fulfillment = $passed ? 100 : 0;
			$details = (!$passed && $results['error']) ? StringUtils::indent(trim($results['error'])) . "\n" : '';
			$this->updateCriterion($goalName, $passed, $fulfillment, $details);
		}
	}
}

