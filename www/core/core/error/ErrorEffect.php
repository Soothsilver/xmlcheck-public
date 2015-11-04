<?php

namespace asm\core;

/**
 * Contains methods for creating messages for often used error effects @module.
 *
 * Most often used error effects are database-related.
 */
class ErrorEffect
{
	/**
	 * Creates message for database insert failure.
	 * @param string $subject item type (lecture, plugin, user, ...)
	 * @return string error effect message
	 */
	public static function dbAdd ($subject)
	{
		return "cannot add new $subject to database";
	}

	/**
	 * Creates message for database update failure.
	 * @param string $subject item type (lecture, plugin, user, ...)
	 * @return string error effect message
	 */
	public static function dbEdit ($subject)
	{
		return "cannot update database with supplied $subject data";
	}

	/**
	 * Creates message for constrained database select failure.
	 * @param string $subject item type (lecture, plugin, user, ...)
	 * @param string $identField key field name
	 * @param mixed $identValue key value (int) or null if message shouldn't contain it
	 * @return string error effect message
	 */
	public static function dbGet ($subject, $identField = 'id', $identValue = null)
	{
		$identString = ($identValue === null) ? " with supplied $identField"
				: " with $identField equal to $identValue";
		return "cannot retrieve {$subject}{$identString} from database";
	}

	/**
	 * Creates message for unconstrained database select failure.
	 * @param string $subject item type (lecture, plugin, user, ...)
	 * @return string error effect message
	 */
	public static function dbGetAll ($subject)
	{
		return "cannot retrieve $subject from database";
	}

	/**
	 * Creates message for failure of single item removal from database.
	 * @param string $subject item type (lecture, plugin, user, ...)
	 * @param string $identField key field name
	 * @param mixed $identValue key value (int) or null if message shouldn't contain it
	 * @return string error effect message
	 */
	public static function dbRemove ($subject, $identField = 'id', $identValue = null)
	{
		$identString = ($identValue === null) ? " with supplied $identField"
				: " with $identField equal to $identValue";
		return "cannot remove {$subject}{$identString} from database";
	}
}

