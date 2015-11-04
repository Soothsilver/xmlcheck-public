<?php

namespace asm\utils;

/**
 * Provides convenience transformation methods for set of named flags and static
 * flag matching function.
 *
 * Transformation methods work only for flag sets of size 31 or smaller.
 */
class Flags
{
	/**
	 * Checks whether supplied set of flags matches at least one of required sets.
	 *
	 * Sample use:
	 * @code
	 * if (!Flags::match($usersPrivileges, PRIVILEGE_1 | PRIVILEGE_2, PRIVILEGE_3))
	 *		throw new \Exception("User doesn't have privileges required for this action");
	 * @endcode
	 * In this case exception is thrown if neither both @c PRIVILEGE_1 and @c PRIVILEGE_2
	 * flags nor @c PRIVILEGE_3 flag are contained in $usersPrivileges.
	 * @param int $set set to match against requirements (one of the others must be included in here)
	 * @param int[] $needsOneOf one of these sets must be included in the first one
	 * @return bool true if @c $set matches at least one set of supplied requirements
	 *		or if no requirements were supplied
	 */
    public static function match ($set, ...$needsOneOf)
	{
		$args = $needsOneOf;
		$matched = empty($args);
		foreach ($args as $flags)
		{
			if (($set & $flags) === $flags)
			{
				$matched = true;
			}
            else
            {
            }
		}
		return $matched;
	}

	/**
	 * Array that converts flag names to their position:
	 * @code
	 * [
	 *  flagName => 0b1
	 *  flagName2 => 0b10
	 *  flagName3 => 0b100
	 *  ...
	 * ]
	 * @endcode
	 * @var array
     */
	protected $flags = [];

	/**
	 * Creates a Flags object. Each flag value (1, 2, 4, ...) receives a name from the input array.
	 *
     * The array must have 31 or less elements. This is a PHP technical limitation.
     *
	 * Sample use:
	 * @code
	 * $privileges = new Flags(array(
	 *		'can do something',
	 *		'can do something else',
	 *		'and this as well',
	 * ));
	 * @endcode
	 * produces Flags instance with flags
	 * @li <tt>can do something</tt> ... 1
	 * @li <tt>can do something else</tt> ... 2
	 * @li <tt>and this as well</tt> ... 4
	 * 
	 * @param array $flagNames flag names
	 */
	public function __construct (array $flagNames)
	{
		if (count($flagNames) > 31)
        {
            throw new \InvalidArgumentException("This class accepts only up to 31 flags.");
        }

		$flag = 1;
		foreach ($flagNames as $name)
		{
			$this->flags[$name] = $flag;
			$flag = $flag << 1;
		}
	}

	/**
	 * Gets flag value for supplied flag name.
	 * @param string $name flag name
	 * @return int flag value if flag name exists, zero otherwise
	 */
	public function getFlag ($name)
	{
		return (isset($this->flags[$name]) ? $this->flags[$name] : 0);
	}

	/**
	 * Turns a supplied integer representing a flag set to flag array.
	 *
	 * Sample use (continuing example from __construct()):
	 * @code
	 * $privileges->toArray($privileges->getFlag('can do something') | $privileges->getFlag('and this as well'));
	 * @endcode
	 * will yield
	 * @code
	 * array(
	 *		'can do something' => true,
	 *		'can do something else' => false,
	 *		'and this as well' => true,
	 * );
	 * @endcode
	 * @param int $flags the set of flags that are true
	 * @return array array with boolean flags indexed by their names
	 */
	public function toArray ($flags)
	{
		$array = [];
		foreach ($this->flags as $name => $flag)
		{
			$array[$name] = self::match($flags, $flag);
		}
		return $array;
	}

	/**
	 * Turns supplied flag array to an integer representing the flags.
	 * @param array $array flag array (as returned from toArray())
	 * @return int flag set (binary union of flag values)
	 */
	public function toInteger ($array)
	{
		$flags = 0;
		foreach ($array as $name => $isSet)
		{
			if ($isSet)
			{
				$flags = $flags | $this->getFlag($name);
			}
		}
		return $flags;
	}
}

