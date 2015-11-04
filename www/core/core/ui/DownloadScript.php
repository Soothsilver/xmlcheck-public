<?php

namespace asm\core;

/**
 * Handler for file download requests (returns file as attachment or error data).
 *
 * @see UiScript for explanation of [stopping] tag.
 */
abstract class DownloadScript extends UiScript
{
	private $outputFileName;		///< file name to suggest to user
	private $outputFile;				///< output file path
	private $outputContentType;	///< content type of output file
	private $attachResult = true;	///< flag indicating whether the result should be attached or passed directly

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
			$this->outputData();
		}
		else
		{
			header('Content-Description: File Download');
			header('Content-Type: ' . $this->outputContentType);
			$disposition = $this->attachResult ?
					'attachment; filename=' . $this->outputFileName :
					'inline';
			header('Content-Disposition: ' . $disposition);
			header('Content-Transfer-Encoding: binary');
			header('Content-Length: ' . filesize($this->outputFile));

			while (ob_get_level()) {
				ob_end_clean();
			}
			flush();
			readfile($this->outputFile);
		}
	}

	/**
	 * Checks whether the output file path is set [stopping].
	 * @return bool true if no error occurred
	 */
	private function isOutputSet ()
	{
		if (!$this->outputFile)
		{
			return $this->stop('no output file is set');
		}

		if (!is_file($this->outputFile))
		{
			return $this->stop('requested file doesn\'t exist or cannot be read');
		}
		
		return true;
	}

	/**
	 * Sets output file path, suggested filename and content type.
	 * @param string $file path of file to output
	 * @param string $name filename to be suggested to user in Save dialog
	 * @param string $contentType content type of file to be output
	 */
	protected final function setOutput ($file, $name = 'select a name', $contentType = 'application/octet-stream')
	{
		$this->outputFile = $file;
		$this->outputFileName = $name;
		$this->outputContentType = $contentType;
	}

	protected final function doNotAttach ()
	{
		$this->attachResult = false;
	}
}

