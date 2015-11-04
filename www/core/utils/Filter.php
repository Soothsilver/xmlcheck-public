<?php

namespace asm\utils;

/**
 * Contains methods for input validation.
 */
class Filter
{
    /**
     * Returns (bool) true if @a $value is "true", "1" or "on"
     * @param $value string
     * @return bool validation success
     */
    public static function isTrue($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN) !== false;
    }

    /**
     * Returns (bool) true if @a $value is formatted like valid e-mail address.
     * @param $value string
     * @return bool validation success
     */
    public static function isEmail($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

	/**
	 * @param string $value
	 * @return bool true if value is alphanumeric
	 */
	public static function isAlphaNumeric ($value)
	{
		return $value === '' || ctype_alnum($value);
	}


	/**
     * Returns true if the value given is a date in the format YYYY-MM-DD.
	 * @param string $value
	 * @return bool true if value is a valid date
	 */
	public static function isDate ($value)
	{
		$dateArray = explode('-', $value);
        if (count($dateArray) !== 3) return false;
        list($year, $month, $day) = $dateArray;
        if (!is_numeric($year) || !is_numeric($month) || !is_numeric($day)) return false;
		return checkdate($month, $day, $year);
	}

	/**
	 * @param string $value
	 * @param array $options
	 *		@arg @c min_length (optional) minimum length
	 *		@arg @c max_length (optional) maximum length
	 * @return bool true if value is a string, possibly with length constrained as specified
	 */
	public static function hasLength ($value, $options)
	{
		return (!isset($options['min_length']) || (strlen($value) >= $options['min_length']))
			&& (!isset($options['max_length']) || (strlen($value) <= $options['max_length']));
	}

	/**
	 * @param string $value
	 * @return bool true if value is not an empty string
	 */
	public static function isNotEmpty ($value)
	{
		return (bool)strlen($value);
	}

	/**
     * Checks whether the value is a non-negative integer (i.e. can be used as an index in a database row)
	 * @param string $value
	 * @return bool true if value is a valid database index
	 */
	public static function isIndex ($value)
	{
		return self::isNonNegativeInt($value);
	}

	/**
     * Checks whether the value is a non-negative integer.
	 * @param string $value
	 * @return bool true if value is a non-negative integer
	 */
	public static function isNonNegativeInt ($value)
	{
		return (filter_var($value, FILTER_VALIDATE_INT, [ 'options' => ['min_range' => 0] ]) !== false);
	}

	/**
     * Checks whether the value is a valid Czech full name. We permit numbers as well.
	 * @param string $value
	 * @return bool true if value is a valid name (contains only letters, numbers and spaces)
	 */
	public static function isName ($value)
	{
		return preg_match('/^[0-9a-zA-ZžščřďťňáéíóúůýěŽŠČŘĎŤŇÁÉÍÓÚÝ ]*$/', $value) === 1 && self::isNotEmpty($value);
	}

	/**
	 * @param string $value
	 * @param array $options
	 * @return bool true if value is one of supplied allowed values
	 */
	public static function isEnum ($value, array $options)
	{
		return in_array($value, $options);
	}
}

