<?php

namespace asm\utils;

/**
 * String-oriented utility functions.
 */
class StringUtils
{
	/**
	 * Prolog to write at the beginning of generated XML fragments
     */
	const xmlProlog = '<?xml version="1.0" standalone="yes"?>';

	/**
	 * Creates random string (length and character set can be specified).
	 * @param int $length length of generated string
	 * @param string $chars used character set
	 * @return string random string
	 */
	public static function randomString ($length = 32,
                                         $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789')
	{
		$maxIndex = strlen($chars) - 1;
		$string = '';
		for ($i = 0; $i < $length; ++$i)
		{
			$string .= $chars{rand(0, $maxIndex)};
		}
		return $string;
	}

	/**
	 * Splits supplied string by uppercase letters and join with delimiters.
	 *
	 * Good for splitting camel- or pascal-case names into 'words'.
	 * @param string $str string to be split
	 * @param string $delimiter string to join split parts with
	 * @return string @a $str with $delimiter inserted before every uppercase letter,
	 *		but not at the beginning
	 */
	public static function splitByUppercase ($str, $delimiter = ' ')
	{
		return preg_replace('/(.)([A-Z])/', "\\1$delimiter\\2", $str);
	}

	/**
	 * Indent string.
	 * @param string $str string to be indented
	 * @param int $count how many padding strings is the string to be indented with
	 * @param string $padStr indentation string
	 * @return string @a $str with @a $padStr inserted @a $count times at the
	 *		beginning of every line
	 */
	public static function indent ($str, $count = 1, $padStr = "\t")
	{
		$lines = explode("\n", $str);
		$padLength = strlen($padStr) * $count;
		foreach ($lines as $i => $line)
		{
			$lines[$i] = str_pad($line, $padLength + strlen($line), $padStr, STR_PAD_LEFT);
		}

		return implode("\n", $lines);
	}

	/**
	 * Turn XML string into readable 'naturally' indented XML.
	 * @param string $xmlString xml string to be indented
	 * @param string $padStr indentation string
	 * @return string indented XML
	 */
	public static function formatXml ($xmlString, $padStr = "\t")
	{
		// add marker linefeeds to aid the pretty-tokenizer (adds a linefeed between all tag-end boundaries)
		$xmlString = preg_replace('/(>)(<)(\/*)/', "$1\n$2$3", $xmlString);

		$xmlString = preg_replace('/(<(\w[^> ]*)(?:[^>]*[^\/])?>)(?:\n)(<\/\2>)/', '$1$3', $xmlString);

		// now indent the tags
		$token = strtok($xmlString, "\n");
		$result = ''; // holds formatted version as it is built
		$pad = 0; // initial indent
		$matches = []; // returns from preg_matches()

		// scan each line and adjust indent based on opening/closing tags
		while ($token !== false)
		{
			// test for the various tag states

			$indent = 0;
			$tagStart = preg_match('/^<\w[^>]*[^\/]>/', $token, $matches);
			$tagEnd = preg_match('/<\/\w[^>]*>$/', $token, $matches);
			if ($tagStart xor $tagEnd)
			{
				if ($tagStart)
				{
					$indent = 1;
				}
				elseif (preg_match('/^[^<]/', $token, $matches))
				{
					$indent = -1;
				}
				else
				{
					--$pad;
				}
			}

			// pad the line with the required number of leading spaces
			$line = str_pad($token, strlen($token) + $pad, $padStr, STR_PAD_LEFT);
			$result .= $line . "\n"; // add to the cumulative result, with linefeed
			$token = strtok("\n"); // get the next token
			$pad += $indent; // update the pad size for subsequent lines
		}

		return $result;
	}

	/**
	 * Remove BOM (byte order mark) from beginning of supplied string.
	 * @param string $str string to remove BOM from
	 * @param[out] string $out string without BOM will be put here
	 * @return bool true if string contained BOM
	 */
	protected static function rmBom ($str, &$out)
	{
		if (substr($str, 0, 3) == pack('CCC', 0xef, 0xbb, 0xbf))
		{
			$out = substr($str, 3);
			return true;
		}

		$out = $str;
		return false;
	}

	/**
	 * Remove BOM (byte order mark) from beginning of supplied string.
	 * @param string $str string to remove BOM from
	 * @return string @a $str without BOM at the beginning
	 */
	public static function removeBom ($str)
	{
		self::rmBom($str, $ret);
		return $ret;
	}

	/**
	 * Remove BOM (byte order mark) from beginning of file.
	 * @param string $filename file path
	 * @return bool false if file doesn't exist, true otherwise
	 */
	public static function removeBomFromFile ($filename)
	{
		if (!file_exists($filename))
		{
			return false;
		}

		if (self::rmBom(file_get_contents($filename), $ret))
		{
			file_put_contents($filename, $ret);
		}

		return true;
	}
	
	/**
	 * Strips namespace from supplied qualified class name.
	 * @param string $className
	 * @return string unqualified class name
	 */
	public static function stripNamespace ($className)
	{
		return substr($className, strrpos($className, '\\') + 1);
	}

	/**
	 * Strips 'comments' from supplied string (comment delimiters must be also supplied).
	 * @param string $string
	 * @param string $startToken comment start delimiter
	 * @param string $endToken comment end delimiter
	 * @param[out] array $comments stripped comments
	 * @return string @a $string stripped of comments
	 */
	public static function stripComments ($string, $startToken, $endToken, &$comments)
	{
		$stl = strlen($startToken);
		$etl = strlen($endToken);
		$comments = [];
		while ((($from = strpos($string, $startToken)) !== false) && (($to = strpos($string, $endToken, $from)) !== false))
		{
			$comments[] = substr($string, $from + $stl, $to - $from - $stl);
			$string = substr($string, 0, $from) . substr($string, $to + $etl);
		}
		return $string;
	}

	/**
	 * Strips function/method links found in PHP errors from supplied string.
	 * @param string $string
	 * @return string
	 */
	public static function stripFunctionLinks ($string)
	{
		return preg_replace('| \[<a href=[^<]*</a>]|', '', $string);
	}
}

