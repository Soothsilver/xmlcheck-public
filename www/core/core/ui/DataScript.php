<?php

namespace asm\core;
use asm\utils\ArrayUtils;

/**
 * Generic handler of core requests from UI (outputs structured data).
 */
abstract class DataScript extends UiScript
{
	private $files = array();	///< associative array with info about files uploaded with request
	private $result = array();	///< data to be returned as output

	protected final function setParams ($params)
	{
		/// This method is restricted (cannot be used by descendants).
	}

	/**
	 * Sanitizes and stores handler arguments and files info.
	 *
	 * @param array $params associative array of script arguments supplied to run() on handler execution
	 * @param array $files associative array with info about files uploaded with request supplied to run() on handler execution
	 */
	protected final function init (array $params = array(), array $files = array())
	{
		$params = array_map('strip_tags', $params);
		$params = array_map('addslashes', $params);

		parent::setParams($params);

		$this->files = $files;
	}

	/**
	 * Gets file info for supplied ID.
	 * @param string $name file ID
	 * @return array file info or null if file with supplied ID wasn't uploaded
	 */
	protected final function getFile ($name)
	{
		return isset($this->files[$name]) ? $this->files[$name] : null;
	}

	/**
	 * Outputs stored data using outputData().
	 * @see outputData()
	 */
	protected final function output ()
	{
		$this->outputData($this->result);
	}

	/**
	 * Merges current result with supplied data.
	 * @param mixed $key single key (string) or associative array with data
	 * @param mixed $value value (used only if $key is string)
	 * @return DataScript self
	 * @see setOutput()
	 */
	protected final function addOutput ($key, $value = null)
	{
		$set = is_array($key) ? $key : array($key => $value);
		$this->result = array_merge($this->result, $set);
		return $this;
	}

	/**
	 * Replaces current result with supplied data.
	 * @param mixed $key single key (string) or associative array with data
	 * @param mixed $value value (used only if $key is string)
	 * @return DataScript self
	 * @see addOutput()
	 */
	protected final function setOutput ($key, $value = null)
	{
		$set = is_array($key) ? $key : array($key => $value);
		$this->result = $set;
		return $this;
	}

    protected final function addRowToOutput($array)
    {
        $this->result[] = $array;
    }

	/**
	 * Strips keys and removes slashes off supplied data and replaces current result
	 * with it.
	 * @param array $table multi-dimensional table-like array
	 * @return DataScript self
	 * @see setOutput()
	 */
	protected final function setOutputTable ($table)
	{
		$table = ArrayUtils::stripKeys($table);
		foreach ($table as &$row)
		{
			foreach ($row as &$cell)
			{
				if (is_string($cell))
				{
					$cell = stripslashes($cell);
				}
			}
		}
		$this->setOutput($table);
		return $this;
	}
}

