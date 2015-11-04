<?php

namespace asm\core;

/**
 * Contains methods to get error messages for predefined errors causes @module.
 */
class ErrorCause
{
	/// static messages for predefined error codes
	protected static $knownCauses = array(
		ErrorCode::lowPrivileges => 'insufficient privileges',
		ErrorCode::corruptedData => 'corrupted data',
		ErrorCode::mail => 'could not send email successfully',
		ErrorCode::dbRequest => 'query to SQL database unsuccessful',
		ErrorCode::dbNameDuplicate => 'database already contains an entry with same unique name',
		ErrorCode::dbEmptyResult => 'database doesn\'t contain required data',
		ErrorCode::zip => 'could not extract supplied zip archive',
		ErrorCode::inputInvalid => 'input not valid',
		ErrorCode::inputIncomplete => 'required input data missing',
		ErrorCode::upload => 'file upload was not successful',
		ErrorCode::pluginLaunch => 'plugin could not be launched',
		ErrorCode::removeFile => 'could not delete file',
		ErrorCode::removeFolder => 'could not remove folder',
		ErrorCode::createFolder => 'could not create new folder',
        ErrorCode::sessionInvalid => 'your session was invalidated',
	);

	protected static $unknownCause = 'cause unknown';	///< message for errors with unknown cause

	/**
	 * Gets error message for supplied error code.
	 * @param int $code error code (ErrorCode constant)
	 * @return string error message
	 */
	public static function getCauseString ($code)
	{
		if (isset(self::$knownCauses[$code]))
		{
			return self::$knownCauses[$code];
		}
		return self::$unknownCause;
	}

	/**
	 * Creates error message for inconsistent user-supplied data (not belonging to single item).
	 * @param string $subject item type (lecture, plugin, user, ...)
	 * @param string $field1 first field name ('id', 'name', 'deadline', ...)
	 * @param string $field2 second field name
	 * @return string error message saying fields don't match
	 */
	public static function dataMismatch ($subject, $field1 = 'id', $field2 = 'name')
	{
		return "$subject $field1 and $field2 don't match";
	}

	/**
	 * Creates error message for user-requested item name being already taken.
	 * @param string $subject item type (lecture, plugin, user, ...)
	 * @param string $name user-supplied name
	 * @return string error message saying name is already taken
	 */
	public static function nameTaken ($subject, $name)
	{
		return "$subject name $name is already taken";
	}

	/**
	 * Creates error message for users requesting access to items they don't own.
	 * @param string $subject item type (group, assignment, ...)
	 * @return string error message
	 */
	public static function notOwned ($subject)
	{
		return "$subject belongs by somebody else";
	}

	/**
	 * Customizes input validation error message.
	 * @param string $hint error message returned by asm::utils::Validator
	 * @param string $fieldName
	 * @param string $placeholder @a $hint part to be replaced by @a $fieldName
	 * @return string @a $hint with @a $placeholder replaced with @a $fieldName
	 */
	public static function invalidInput ($hint, $fieldName, $placeholder = 'value')
	{
		return str_replace($placeholder, $fieldName, $hint);
	}
}

