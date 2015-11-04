<?php

namespace asm\docs;
use InvalidArgumentException;

require_once __DIR__ . "/IInputFilter.php";

/**
 * Joins input filters together to use first one that understands given code.
 */
class InputFilterSet implements IInputFilter
{
	protected $filters;

	/**
	 * Initializes instance with supplied filters.
	 * @param IInputFilter [...] input filters
	 */
	public function __construct ()
	{
		$filters = func_get_args();
		if (count($filters) < 1)
		{
			throw new InvalidArgumentException('At least one filter must be supplied');
		}

		foreach ($filters as $filter)
		{
			if (!($filter instanceof IInputFilter))
			{
				throw new InvalidArgumentException('Arguments must implement IInputFilter.');
			}
		}

		$this->filters = $filters;
	}

	/**
	 * Applies contained filters on supplied code to find one that understands the
	 * code.
	 * @param string $code documented code
	 * @return mixed @a $code filtered by first of the contained filters that
	 *		understands it (string), or null if none of them understand it
	 */
	public function apply ($code)
	{
		foreach ($this->filters as $filter)
		{
			$filtered = $filter->apply($code);
			if ($filtered !== null)
			{
				return $filtered;
			}
		}
		return null;
	}
}

?>