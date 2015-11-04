<?php

namespace asm\plugin;
use asm\utils\Utils, asm\utils\StringUtils, SimpleXMLElement;
use SebastianBergmann\Exporter\Exception;


/**
 * Wrapper for plugin response [immutable].
 *
 * When launched, plugins return a set of criteria with information about which
 * of them were met, which not and why, etc. They return it either directly as
 * PluginResponse instance (in case of PHP plugins) or as XML, which must be
 * transformed using fromXml() function.
 */
class PluginResponse
{
	protected $results = array();	///< array of criteria info
	protected $output = null;		///< path of ZIP archive with plugin output
	protected $error = null;		///< error message in case plugin failed

	protected $fulfillment = 0;	///< fulfillment percentage
	protected $details = '';		///< all criteria info in a single readable string

	/**
	 * Creates PluginResponse instance in case of plugin successfully finishing.
	 * @param array $criteria criteria info
	 * @param mixed $output path of ZIP archive with plugin output
	 * @return PluginResponse instance
	 * @see createError()
	 */
	public static function create ($criteria, $output = null)
	{
		return new self($criteria, $output);
	}

	/**
	 * Creates PluginResponse instance in case of plugin failure.
	 * @param string $error error message
	 * @return PluginResponse instance
	 * @see create()
	 */
	public static function createError ($error)
	{
		return new self($error);
	}

	/**
	 * Creates a PluginResponse instance with data parsed from XML (as returned from toXml()).
	 * Note that it also removes all data before the start of the XML prolog. This is due to a probable bug in
	 * the Java Base Library, where, in certain timing, streams are not redirected before being output and thus text
	 * printed by the user may be printed into the output stream where only the XML data should be.
	 *
	 * @param string $responseString String given as the standard output of a Java plugin.
	 * @return PluginResponse The parsed response.
	 */
	public static function fromXmlString($responseString)
	{
		$prologPosition = strpos($responseString, "<?xml");
		if ($prologPosition === false) {
			throw new Exception("The plugin did not return XML data because it did not return a XML prolog.");
		}
		$responseString = substr($responseString, $prologPosition);
		return self::fromXml(simplexml_load_string($responseString));
	}

	/**
	 * Creates a PluginResponse instance with data parsed from XML (as returned from toXml()).
	 * @param SimpleXMLElement $xml
	 * @return PluginResponse instance
	 * @see toXml()
	 */
	private static function fromXml (SimpleXMLElement $xml)
	{
		/** @noinspection PhpUndefinedFieldInspection */
		if ($xml->error)
		{
			/** @noinspection PhpUndefinedFieldInspection */
			return self::createError((string)$xml->error);
		}

		/** @noinspection PhpUndefinedFieldInspection */
		$output = ($xml->output) ? (string)$xml->output->file : null;

		$criteria = array();
		/** @noinspection PhpUndefinedFieldInspection */
		foreach ($xml->criterion as $criterion)
		{
			/** @noinspection PhpUndefinedFieldInspection */
			$criteria[(string)$criterion['name']] = array(
				'passed' => Utils::parseBool((string)$criterion->passed),
				'fulfillment' => (int)(string)$criterion->fulfillment,
				'details' => (string)$criterion->details,
			);
		}
		
		return self::create($criteria, $output);
	}

	/**
	 * Initializes instance members with supplied data.
	 * @param mixed $data either error message (string) or criteria info (array)
	 * @see create()
	 * @see createError()
	 * @see fromXml()
	 */
	protected function __construct ($data)
	{
		if (!is_array($data))
		{
			$this->error = StringUtils::stripFunctionLinks($data);
		}
		else
		{
			$this->results = $data;
			$this->output = func_get_arg(1);

			$fulfillmentSum = 0;
			$passedCriteria = 0;

			$this->details = '';
			foreach ($data as $name => $criterion)
			{
				$fulfillmentSum += $criterion['fulfillment'];
				$passedCriteria += (int)$criterion['passed'];

				$this->details .= $name . ' ... ' . ($criterion['passed'] ? 'PASSED' : 'FAILED') . "\n";
				if ($criterion['details'])
				{
					$this->details .= $criterion['details'] . "\n";
				}
			}

			$numCriteria = count($data);
			$this->details = "Passed $passedCriteria out of $numCriteria criteria.\n\n{$this->details}";
			$this->fulfillment = $numCriteria ? ($fulfillmentSum / $numCriteria) : 0;
		}
	}

	/**
	 * Turns contained data to XML.
	 * @return SimpleXmlElement XML (see Interfaces section of @ref core)
	 */
	public function toXml ()
	{
		$xml = new SimpleXMLElement(StringUtils::xmlProlog . '<plugin-reply></plugin-reply>');

		if ($this->isSuccessful())
		{
			if ($this->hasOutput())
			{
				$output = $xml->addChild('output');
				$output->addChild('file', $this->getOutput());
			}
			foreach ($this->getResults() as $name => $criterionData)
			{
				$criterion = $xml->addChild('criterion');
				$criterion->addAttribute('name', $name);
				$criterion->addChild('passed', $criterionData['passed'] ? 'true' : 'false');
				$criterion->addChild('fulfillment', $criterionData['fulfillment']);
				$criterion->addChild('details', htmlspecialchars($criterionData['details']));
			}
		}
		else
		{
			$xml->addChild('error', $this->getDetails());
		}

		return $xml;
	}

	/**
	 * Checks whether this is 'success' or 'error' response.
	 * @return bool true if this is a 'success' response
	 */
	public function isSuccessful ()
	{
		return ($this->error == null);
	}

	/**
	 * Gets criteria info.
	 * @return array criteria info (or null for 'error' response)
	 */
	public function getResults ()
	{
		return $this->results;
	}

	/**
	 * Checks whether path of plugin output is set.
	 * @return bool true if path to plugin output is set
	 */
	public function hasOutput ()
	{
		return ($this->output != null);
	}

	/**
	 * Gets fulfillment percentage.
	 * @return int fulfillment (0 to 100)
	 */
	public function getFulfillment ()
	{
		return $this->fulfillment;
	}

	/**
	 * Gets path of plugin output.
	 * @return mixed path of ZIP archive with plugin output (or null if not set)
	 */
	public function getOutput ()
	{
		return $this->output;
	}

	/**
	 * Gets response info in a readable string.
	 * @return string criteria info or error message depending on whether the
	 *		plugin finished successfully
	 */
	public function getDetails ()
	{
		if ($this->isSuccessful())
		{
			return $this->details;
		}
		return $this->error;
	}
}

