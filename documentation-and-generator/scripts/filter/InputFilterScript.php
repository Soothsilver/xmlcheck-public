<?php

namespace asm\docs;
require_once __DIR__ . '/IInputFilter.php';
use InvalidArgumentException;

/**
 * Stand-alone script for application of input filter on source file.
 */
class InputFilterScript
{
	/**
	 * @var IInputFilter The filter to apply.
     */
	protected $filter;

	/**
	 * Initializes instance with supplied input filter.
	 * @param IInputFilter $filter
	 */
	public function __construct (IInputFilter $filter)
	{
		$this->filter = $filter;
	}

	/**
	 * Runs script.
	 * @param int $argc number of script arguments
	 * @param array $argv array with script arguments
	 * @param bool $print false to just return filtered input (otherwise it is
	 *		printed to output)
	 * @return mixed filtered input (string) if @a $return is set to true, nothing
	 *		otherwise
	 * @throws InvalidArgumentException in case @a $argv doesn't contain at least
	 *		one argument or if it isn't a path to readable file
	 */
	public function run ($argc, array $argv, $print = true)
	{
		if ($argc <= 1) {
			throw new InvalidArgumentException('Script requires at least one argument (input file path).');
		}

		if (!is_readable($argv[1])) {
			throw new InvalidArgumentException('Input file doesn\'t exist or cannot be read.');
		}

		$filtered = $this->filter->apply(file_get_contents($argv[1]));
		if ($print)
		{
			echo $filtered;
		}
		
		return $filtered;
	}
}

?>