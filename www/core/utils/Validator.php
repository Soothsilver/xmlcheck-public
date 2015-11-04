<?php

namespace asm\utils;

/**
 * Facilitates input validation and returning of appropriate errors @module.
 * @see Filter
 */
class Validator
{
	/**
	 * Creates appropriate error message (validation hint) for supplied filter name
	 * and options.
	 * @param string $filter filter name (one of @ref Filter "Filter class" methods)
	 * @param array $options filter-specific options
	 * @return mixed error message (string) or true for unknown filters
	 */
	protected static function getError ($filter, array $options = [])
	{
		switch ($filter)
		{
			case 'isTrue':
				return 'value is neither 1, on nor true';
			case 'isEmail':
				return 'value is not a valid e-mail address';
			case 'isAlphaNumeric':
				return 'value is not alphanumeric';
			case 'isDate':
				return 'value is not a correctly formatted date';
			case 'hasLength':
				$restrictions = [];
				if (isset($options['min_length']))
				{
					array_push($restrictions, 'minimum length of ' . $options['min_length']);
				}
				if (isset($options['max_length']))
				{
					array_push($restrictions, 'maximum length of ' . $options['max_length']);
				}
				return 'value has to have ' . implode(' and ', $restrictions);
			case 'isNotEmpty':
				return 'value must not be empty';
			case 'isIndex':
			case 'isNonNegativeInt':
				return 'value must be a non-negative integer';
			case 'isName':
				return 'value must consist only of letters and spaces';
			case 'isEnum':
				return 'value must be one of: ' . implode(', ', $options);
			default:
				return true;
		}
	}

	/**
	 * Validates supplied value using supplied filter w/ options.
	 * @param mixed $value value to be validated
	 * @param mixed $filter filter function (callback returning false in case of
	 *		success and true or error message string in case of failure) or name
	 *		of one of filtering methods of @ref Filter "Filter class"
	 * @param array $options filter-specific options
	 * @return mixed false in case of success and true or error message string
	 *		in case of failure
	 */
	public static function validate ($value, $filter, array $options = [])
	{
		if (is_callable($filter))
		{
			return !call_user_func($filter, $value, $options);
		}
		elseif (!Filter::$filter($value, $options))
		{
			return self::getError($filter, $options);
		}
		return false;
	}
}

