<?php

namespace asm\core;
use InvalidArgumentException;

/**
 * Wrapper for response to core request from UI.
 */
class UiResponse
{
	/**
	 * Creates UiResponse instance from supplied data and errors.
	 * @param array $data response data
	 * @param array $errors errors
	 * @return UiResponse response instance
	 */
	public static function create (array $data, array $errors = array())
	{
		return new self($data, $errors);
	}

	protected $data = array();		///< response data
	protected $errors = array();	///< errors

	/**
	 * Sets response data and errors.
	 * @param array $data response data
	 * @param array $errors errors
	 * @throws InvalidArgumentException in case $errors is not an array of Error descendants
	 */
	protected function __construct (array $data, array $errors)
	{
		$this->data = $data;

		foreach ($errors as $error)
		{
			if (!is_a($error, 'asm\core\Error'))
			{
				throw new InvalidArgumentException("Second argument must be an array of Error descendants");
			}
		}
		$this->errors = $errors;
	}

	/**
	 * Packs response to JSON string.
	 * @return string JSON-encoded response data
	 */
	public function toJson ()
	{
		$output = array('data' => $this->data);

		if (!empty($this->errors))
		{
			$output['errors'] = array();
			foreach ($this->errors as $error)
			{
				/**
				 * @var $error \asm\core\Error
				 */
				$output['errors'][] = $error->toArray();
			}
		}

		return json_encode($output);
	}
}

