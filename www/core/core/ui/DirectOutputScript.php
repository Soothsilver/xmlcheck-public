<?php

namespace asm\core;

/**
 * Handler for file download requests (returns file as attachment or error data).
 *
 * @see UiScript for explanation of [stopping] tag.
 */
abstract class DirectOutputScript extends UiScript
{
	private $outputContentDescription = 'Direct download';	///< description of the output
	private $outputContentType = 'text/plain';	///< content type of output file

	protected final function setParams ($params)
	{
		/// This method is restricted (cannot be used by descendants).
	}

	/**
	 * Sanitizes and stores handler arguments.
	 *
	 * @param array $params associative array of script arguments supplied to run() on handler execution
	 * @param array $files unused
	 */
	protected final function init (array $params = array(), array $files = array())
	{
		$params = array_map('strip_tags', $params);
		$params = array_map('addslashes', $params);

		parent::setParams($params);

		ob_start();
	}

	/**
	 * Outputs requested file or error data in case of handler failure.
	 *
	 * In case of handler success no other data apart from the file itself are sent.
	 */
	protected final function output ()
	{
		if ($this->isFailed() || !$this->isOutputSet())
		{
			while (ob_get_level()) {
				ob_end_clean();
			}
			ob_start();
			
			$this->setContentType('text/plain');

			$this->outputData();
		}

		header('Content-Description: ' . $this->outputContentDescription);
		header('Content-Type: ' . $this->outputContentType);
		header('Content-Disposition: inline');
		header('Content-Transfer-Encoding: binary');

		ob_end_flush();
	}

	/**
	 * Checks whether the output file path is set [stopping].
	 * @return bool true if no error occurred
	 */
	private function isOutputSet ()
	{
		if (ob_get_contents() == '')
		{
			return $this->stop('no output has been created');
		}

		return true;
	}

	/**
	 * Sets content type of the output.
	 * @param string $contentType content type of file to be output
	 */
	protected final function setContentType ($contentType)
	{
		$this->outputContentType = $contentType;
	}

	/**
	 * Sets content description of the output.
	 * @param string $description content description
	 */
	protected final function setOutputDescription ($description)
	{
		$this->outputContentDescription = $description;
	}
}

