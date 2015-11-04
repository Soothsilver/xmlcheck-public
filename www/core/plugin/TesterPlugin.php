<?php

namespace asm\plugin;
use asm\utils\StringUtils;

/**
 * Plugin containing one or more tests and using their goals just as basis for
 * its criteria (not as template).
 *
 * Criteria are not bound to single test goals (or even tests) and are added
 * separately from tests. However, the class still manages running of tests and
 * translation of test results to criteria (using relationships between test
 * goals and plugin criteria supplied on criteria creation).
 * 
 * @see SingleTestPlugin
 */
abstract class TesterPlugin extends Plugin {

	/**
	 * @var $tests Test[]
	 */
	private $tests = array();			///< managed tests
	private $sources = array();		///< sources for manages tests
	private $params = array();			///< parameters for managed tests
	private $testResults = array();	///< results of managed tests
	/// relationships between plugin criteria and goals of managed tests
	private $testerCriteria = array();


	/**
	 * Adds test to be managed by this plugin.
	 * @param string $id test ID (unique)
	 * @param Test $test
	 * @param array $sources sources the test is to be run with
	 * @param array $params parameters the test is to be run with
	 * @see addTesterCriterion()
	 */
	protected final function addTest ($id, Test $test, array $sources, array $params = array())
	{
		$this->tests[$id] = $test;
		$this->sources[$id] = $this->makeFullSources($sources);
		$this->params[$id] = $params;
	}

	/**
	 * Adds new criterion based on selected goals of managed tests.
	 * @param string $name descriptive criterion name
	 * @param array $config test IDs as keys and arrays of goal IDs as values,
	 *		or null to use all use all goals of that particular test
	 * @see addTest()
	 */
	protected final function addTesterCriterion ($name, array $config)
	{
		$this->addCriterion($name);
		$this->testerCriteria[$name] = $config;
	}

	/**
	 * Executes managed tests and turns their results to plugin criteria (main
	 * plugin logic).
	 * @see addTest()
	 * @see addTesterCriterion()
	 */
	protected final function execute ()
	{
		foreach ($this->tests as $id => $test)
		{
			$this->testResults[$id] = $test->run($this->sources[$id], $this->params[$id]);
		}
		$this->checkTesterCriteria();
	}

	/**
	 * Turns managed tests' results to plugin criteria or errors.
	 * For criterion to be passed, all goals specified for that criterion when
	 * @ref addTesterCriterion() "adding it" must be reached. Otherwise the
	 * criterion is failed with fulfillment percentage based on number of failed
	 * goals and failure info created by joining failure information of failed goals.
	 */
	private function checkTesterCriteria ()
	{
		$failedTests = array();

		foreach ($this->testerCriteria as $criterionName => $criterionConfig)
		{
			$goalCount = 0;
			$goalsFailed = 0;
			$details = '';

			foreach ($criterionConfig as $testId => $goalIds)
			{
				if ($goalIds === null)
				{
					$goalIds = $criterionConfig[$testId] = array_keys($this->tests[$testId]->getGoals());
				}
				$goalCount += count($goalIds);

				$results = $this->testResults[$testId];

				foreach ($goalIds as $goalId)
				{
					$goal = isset($results['results'][$goalId]) ? $results['results'][$goalId]
							: array();
					if (count($results['errors']))
					{
						++$goalsFailed;
						if (!$failedTests[$testId])
						{
							$failedTests[$testId] = true;
							$details .= $results['name'] . ' didn\'t successfully finish' . "\n"
									. StringUtils::indent(implode("\n", $results['errors']));
						}
					}
					elseif (!$goal['reached'])
					{
						++$goalsFailed;
						$errorStr = $goal['error'] ? StringUtils::indent(trim($goal['error'])) . "\n" : '';
						$details .= "{$goal['name']} ({$results['name']}) failed\n" . $errorStr;
					}
				}
			}

			$fulfillment = ($goalCount > 0)
					? floor(($goalCount - $goalsFailed) * 100 / $goalCount)
					: 100;

			$this->updateCriterion($criterionName, ($goalsFailed == 0), $fulfillment, $details);
		}
	}
}

