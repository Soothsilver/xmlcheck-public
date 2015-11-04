<?php

namespace asm\plugin;
use InvalidArgumentException, DOMDocument, SimpleXMLElement;

/**
 * Base for tests for XML input checking.
 *
 * Contains convenience methods for XML loading and checking.
 */
abstract class XmlTest extends Test
{
    /**
     * Loads and parses XML file to get XML DOM.
     * @param string $xmlFile XML file path
     * @param boolean $performDtdValidation
     * @param \DOMDocument $xmlDom
     * @param $error
     * @return bool true if loading and parsing was successful
     */
	protected function loadXml ($xmlFile, $performDtdValidation, &$xmlDom, &$error)
	{
		$this->useLibxmlErrors();
		$xmlDom = new DOMDocument();
		$xmlDom->preserveWhiteSpace = false;
		$xmlDom->formatOutput = true;
        // libxml_use_internal_errors(false);
		if (!$xmlDom->load($xmlFile, LIBXML_DTDLOAD | LIBXML_DTDATTR | LIBXML_NOENT | ($performDtdValidation ? LIBXML_DTDVALID : 0)))
        {
          $errors = libxml_get_errors();
          foreach ($errors as $libxmlError)
          {
              if ($libxmlError->code == 25)
              {
                  // This error may, among others, be caused by the use of '%' sign within a notation declaration.
                  // This is permitted in XML standard, but due to a bug in LibXML, the parser is unable to cope.
                  // Workaround around bug: https://bugzilla.gnome.org/show_bug.cgi?id=727768
                  $error = "Your DTD perhaps contains an unterminated parameter entity reference. It is also possible you used the percent ('%') sign within a NOTATION value or CDATA section. This is OK and the standard permits this but due to a bug in libxml2 (https://bugzilla.gnome.org/show_bug.cgi?id=727768), it is not possible to load your file. Please upload a document without using the percent sign in that case. We apologize for the inconvenience.";
                  return false;
              }
          }
        }

		$errors = libxml_get_errors();
        libxml_clear_errors();
		if ($errors)
		{
			$basename = basename($xmlFile);
            $allErrors = "";
            foreach($errors as $error)
            {
                /**
                 * @var $error \LibXMLError
                 */
                $message = trim($error->message);
                $message = $this->makeFriendlier($message);
                $libxmlMessage = $message . " at " . $error->file . ", line " . $error->line . ", libxml error code " . $error->code;
                if ($error->level === LIBXML_ERR_WARNING)
                {
                    $libxmlMessage .= ". This is only a warning and not an error. This does not make your document ill-formed or invalid. However, you must avoid warnings in this assignment.";
                }
                $allErrors .= $libxmlMessage . "\n";
            }
			$error = "The file in '$basename' is not valid XML (errors triggered: " . count($errors) . "):\n" . $allErrors;
			return false;
		}
		return true;
	}
    private function makeFriendlier($xmlErrorMessage)
    {
        if ($xmlErrorMessage === "Space needed here")
        {
            return "Space needed here (a common error is not putting the encoding in the XML declaration of a DTD, or not putting a space after a SYSTEM token, or forgetting the question mark at the end of the XML prolog).";
        }
        else if ($xmlErrorMessage === "Blank needed here")
        {
            return "Blank needed here (a common error is not putting the encoding in the XML declaration of a DTD, or not putting a space after a SYSTEM token, or forgetting the question mark at the end of the XML prolog).";
        }
        else if (strpos($xmlErrorMessage, " not defined") !== false)
        {
            return $xmlErrorMessage . " (did you refer to the correct DTD file in your DOCTYPE?)";
        }
        else
        {
            return $xmlErrorMessage;
        }
    }

    private function utf16_to_utf8($str) {
        $c0 = ord($str[0]);
        $c1 = ord($str[1]);

        if ($c0 == 0xFE && $c1 == 0xFF) {
            $be = true;
        } else if ($c0 == 0xFF && $c1 == 0xFE) {
            $be = false;
        } else {
            return $str;
        }

        $str = substr($str, 2);
        $len = strlen($str);
        $dec = '';
        for ($i = 0; $i < $len; $i += 2) {
            $c = ($be) ? ord($str[$i]) << 8 | ord($str[$i + 1]) :
                ord($str[$i + 1]) << 8 | ord($str[$i]);
            if ($c >= 0x0001 && $c <= 0x007F) {
                $dec .= chr($c);
            } else if ($c > 0x07FF) {
                $dec .= chr(0xE0 | (($c >> 12) & 0x0F));
                $dec .= chr(0x80 | (($c >>  6) & 0x3F));
                $dec .= chr(0x80 | (($c >>  0) & 0x3F));
            } else {
                $dec .= chr(0xC0 | (($c >>  6) & 0x1F));
                $dec .= chr(0x80 | (($c >>  0) & 0x3F));
            }
        }
        return $dec;
    }
    protected function convertToUtf8($string)
    {
        if (mb_check_encoding($string, "UTF-8"))
        {
            return $string;
        }
        else if (mb_check_encoding($string, "UTF-16"))
        {
            return $this->utf16_to_utf8($string);
        }
        else if (mb_check_encoding($string, "UTF-16LE"))
        {
            return $this->utf16_to_utf8($string);
        }
        else if (mb_check_encoding($string, "UTF-16BE"))
        {
            return $this->utf16_to_utf8($string);
        }
        else {
            return false;
        }
    }

	/**
	 * Checks XML for existence of certain nodes using XPath queries.
	 * @param SimpleXMLElement $sourceXML
	 * @param string $goalId goal ID
	 * @param array $config array to be accepted by CountedRequirements constructor
	 *		(@c extras must contain @c 'xpath' element, or they will be replaced by
	 *		array with @c 'xpath' as single element)
	 * @return bool true if goal was reached
	 */
	protected function checkUsingXpath(SimpleXMLElement $sourceXML, $goalId, $config)
	{
		if (!isset($config['extras']) || !in_array('xpath', $config['extras']))
		{
			$config = array_merge($config, array('extras' => array('xpath')));
		}
		
		$requirements = new CountedRequirements($config);
		foreach ($requirements->getNames() as $name)
		{
			$results = $sourceXML->xpath($requirements->getExtra('xpath', $name));
			if ($results)
			{
				$requirements->addOccurrences($name, count($results));
			}
		}
		return $this->resolveCountedRequirements($requirements, $goalId);
	}

	/**
	 * Turns on special libxml error handling.
	 * @see getLibxmlErrors()
	 * @see reachGoalOnNoLibxmlErrors()
	 */
	protected function useLibxmlErrors ()
	{
		libxml_use_internal_errors(true);
		libxml_clear_errors();
	}

	/**
	 * Gets stores libxml errors and turns off special libxml error handling.
	 * @return array errors caught since special libxml error handling was turned on
	 * @see useLibxmlErrors()
	 * @see reachGoalOnNoLibxmlErrors()
	 */
	protected function getLibxmlErrors ()
	{
        $errors = libxml_get_errors();
        libxml_clear_errors();
        libxml_use_internal_errors();
        return $errors;
	}

	/**
	 * Marks goal as failed if some libxml errors were caught and turns off special
	 * libxml error handling.
	 * @note Unlike reachGoalOnNoLibxmlErrors(), this method does not mark the goal
	 *		as reached if no errors were caught.
	 * @param string $goalId goal ID
	 * @param string $sourceId ID of source file containing source XML
	 * @param mixed $dropLevels error levels to be dropped, or null to use default
	 * @return bool true if no errors were caught, false otherwise
	 * @see useLibxmlErrors()
	 * @see reachGoalOnNoLibxmlErrors()
	 */
	protected function failGoalOnLibxmlErrors ($goalId, $sourceId, $dropLevels = null)
	{
		$errors = ($dropLevels !== null) ? $this->getLibxmlErrors($dropLevels)
				: $this->getLibxmlErrors();

		if (!empty($errors))
		{

			$message = implode("\n", array_map(function ($error) {
				return $error->message;
			}, $errors));
			return $this->failGoal($goalId, $message, $errors[0]->line, $sourceId);
		}
		
		return true;
	}

	/**
	 * Marks goal as reached if no libxml errors were caught and turns off special
	 * libxml error handling.
	 * If some libxml errors were caught, goal is marked as failed with last caught
	 * error as failure info.
	 * @param string $goalId goal ID
	 * @param string $sourceId ID of source file containing source XML
	 * @param mixed $dropLevels error levels to be dropped, or null to use default
	 * @return bool true if the goal was reached
	 * @see useLibxmlErrors()
	 * @see failGoalOnLibxmlErrors()
	 */
	protected function reachGoalOnNoLibxmlErrors ($goalId, $sourceId, $dropLevels = null)
	{
		if ($this->failGoalOnLibxmlErrors($goalId, $sourceId, $dropLevels))
		{
			return $this->reachGoal($goalId);
		}
		return false;
	}

	/**
	 * Turns result from DOMXpath::evaluate() or DOMXpath::query() to array.
	 * @param mixed $nodes either a DOMNodeList or descendant of DOMNode
	 * @return array DOMNode elements
	 * @throws InvalidArgumentException in case @c $nodes is of incorrect type
	 */
	protected function domXpathEvalToArray ($nodes)
	{
		$array = array();

		if (is_a($nodes, 'DOMNodeList'))
		{
			for ($i = 0; $i < $nodes->length; ++$i)
			{
				$array[] = $nodes->item($i);
			}
		}
		elseif (is_a($nodes, 'DOMNode'))
		{
			$array[] = $nodes;
		}
		else
		{
			throw new InvalidArgumentException('Argument must be either a DOMNodeList or descendant of DOMNode. Instead, it is: ' . gettype($nodes));
		}

		return $array;
	}
}

