<?php

namespace asm\core;
use Exception;

/**
 * Wrapper for core request from UI.
 */
class UiRequest
{
	const handlerNsPrefix = 'asm\core\\';	///< (string) namespace prefix of request handler classes

	/**
	 * Creates UiRequest instance from array.
	 * @param array $array containing 'action' key with request name and request arguments
	 * @return UiRequest request instance
	 * @throws CoreException in case 'action' is not set or valid
	 */
	public static function fromArray ($array)
	{
		if (!isset($array['action']))
		{
			throw new CoreException('Request data must contain "action" field');
		}

		$requestName = $array['action'];
		unset($array['action']);

		$handler = self::handlerNsPrefix . $requestName;
		
		try
		{
			$classExists = class_exists($handler);
		}
		catch (Exception $e)
		{
			$classExists = false;
		}

		if (!$classExists || !is_subclass_of($handler, 'asm\core\UiScript'))
		{
			throw new CoreException("Undefined request \"$requestName\"");
		}

		return new self($requestName, $array);
	}

	protected $requestName = null;	///< (string) request name
	protected $params = array();		///< (array) request arguments

	/**
	 * Sets request name and arguments.
	 * @param string $requestName request name
	 * @param array $params request arguments
	 */
	protected function __construct ($requestName, array $params = array())
	{
		$this->requestName = $requestName;
		$this->params = $params;
	}


	/**
	 * Gets request name.
	 * @return string request name
	 */
	public function getRequestName ()
	{
		return $this->requestName;
	}

	/**
	 * Gets request handler instance.
	 * @return UiScript request handler
	 */
	public function getHandler ()
	{
		$handlerClass = self::handlerNsPrefix . $this->requestName;
		return new $handlerClass();
	}

	/**
	 * Gets request arguments.
	 * @return array request arguments
	 */
	public function getParams ()
	{
		return $this->params;
	}
}

