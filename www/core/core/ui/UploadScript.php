<?php

namespace asm\core;

/**
 * Handler for file upload requests (returns IDs of stored files).
 *
 * @see UploadManager
 */
abstract class UploadScript extends UiScript
{
	private $uploadedFileIds = array();	///< associative array with IDs belonging to uploaded file names

	protected final function setParams ($params)
	{
		/// This method is restricted (cannot be used by descendants).
	}

	/**
	 * Stores uploaded files information as handler arguments.
	 *
	 * @param array $params unused
	 * @param array $files associative array with info about files uploaded with request supplied to run() on handler execution
	 */
	protected final function init (array $params = array(), array $files = array())
	{
		parent::setParams($files);
	}

	/**
	 * Outputs array with file names as keys and IDs as values using outputData().
	 * @see outputData()
	 */
	protected final function output ()
	{
		$this->outputData($this->uploadedFileIds);
	}

	/**
	 * Add {\<file name\> => \<storage ID\>} pair to result.
	 * @param string $name file name (key under which the file was uploaded)
	 * @param string $id storage ID
	 */
	protected final function addUploadedFileId ($name, $id)
	{
		$this->uploadedFileIds[$name] = $id;
	}
}

