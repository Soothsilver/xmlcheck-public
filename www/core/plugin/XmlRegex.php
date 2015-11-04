<?php

namespace asm\plugin;

/**
 * Provides means for getting regular expressions matching entities found in XML
 * and DTD files according to W3C specifications.
 *
 * See @ref __construct() "constructor documentation" for description of getting
 * defined expressions.
 *
 * Sample use:
 * @code
 * $xmlRegex = XmlRegex::getInstance(XmlRegex::WRAP_PERL);
 * $commentRegex = $xmlRegex->Comment;
 * @endcode
 * @c $commentRegex contains Perl-compatible regular expression matching XML
 * comments after above code is run.
 */
class XmlRegex
{
	/// @name Flags used when wrapping regular expressions with wrap()
	//@{
	const CASE_INSENSITIVE = 1;	///< case-insensitive
	const DOT_MATCH_ALL = 2;		///< dot matches all characters (even newlines)
	const LINE_ANCHORS = 4;			///< @c ^ and @c $ match line start and end respectively
	const FREE_SPACING_MODE = 8;	///< free-spacing mode
	//@}

	/// @name Wrapping modes used by wrap()
	//@{
	const WRAP_NONE = 1;	///< no wrapping
	const WRAP_PERL = 2;	///< Perl-compatible wrapping (for preg_* functions)
	//@}

	protected static $instance = null;	///< singleton instance
	
	protected $_expressions = array();	///< contained regular expressions
	protected $_locked = false;			///< write lock (expressions can only be added in constructor)
	protected $_flags = 0;					///< wrapping flags
	protected $_wrap = 0;					///< wrapping mode

	/**
	 * Initializes instance with all predefined regular expressions.
	 *
	 * See W3C specification of XML & DTD for expression names (expressions are
	 * accessible under names used in W3C spec for entities matched by them).
	 */
	protected function __construct ()
	{
		$this->CommentChar = "[\\x9\\xA\\xD\\x20-\\x2C\\x2E-\\x{D7FF}\\x{E000}-\\x{FFFD}\\x{10000}-\\x{10FFFF}]";
		//$this->Char = "{$this->CommentChar}|\\x2D";
		$this->Char = "[\\x9\\xA\\xD\\x20-\\x{D7FF}\\x{E000}-\\x{FFFD}\\x{10000}-\\x{10FFFF}]";
		$this->S = "[\\x20\\x9\\xD\\xA]";
		$this->NameStartCharNonXxMmLl = "[:A-KN-WYZ_a-kn-wyz\\xC0-\\xD6\\xD8-\\xF6\\xF8-\\x{2FF}\\x{370}-\\x{37D}\\x{37F}-\\x{1FFF}\\x{200C}-\\x{200D}\\x{2070}-\\x{218F}\\x{2C00}-\\x{2FEF}\\x{3001}-\\x{D7FF}\\x{F900}-\\x{FDCF}\\x{FDF0}-\\x{FFFD}\\x{10000}-\\x{EFFFF}]";
		$this->NameStartCharNonXx = "[:A-WYZ_a-wyz\\xC0-\\xD6\\xD8-\\xF6\\xF8-\\x{2FF}\\x{370}-\\x{37D}\\x{37F}-\\x{1FFF}\\x{200C}-\\x{200D}\\x{2070}-\\x{218F}\\x{2C00}-\\x{2FEF}\\x{3001}-\\x{D7FF}\\x{F900}-\\x{FDCF}\\x{FDF0}-\\x{FFFD}\\x{10000}-\\x{EFFFF}]";
		$this->NameStartChar = "[:A-Z_a-z\-.0-9\\xB7\\xC0-\\xD6\\xD8-\\xF6\\xF8-\\x{37D}\\x{37F}-\\x{1FFF}\\x{200C}-\\x{200D}\\x{203F}-\\x{2040}\\x{2070}-\\x{218F}\\x{2C00}-\\x{2FEF}\\x{3001}-\\x{D7FF}\\x{F900}-\\x{FDCF}\\x{FDF0}-\\x{FFFD}\\x{10000}-\\x{EFFFF}]";
		$this->NameCharNonMm = "[:A-LN-Z_a-ln-z\\xC0-\\xD6\\xD8-\\xF6\\xF8-\\x{2FF}\\x{370}-\\x{37D}\\x{37F}-\\x{1FFF}\\x{200C}-\\x{200D}\\x{2070}-\\x{218F}\\x{2C00}-\\x{2FEF}\\x{3001}-\\x{D7FF}\\x{F900}-\\x{FDCF}\\x{FDF0}-\\x{FFFD}\\x{10000}-\\x{EFFFF}]";
		$this->NameCharNonLl = "[:A-KM-Z_a-km-z\\xC0-\\xD6\\xD8-\\xF6\\xF8-\\x{2FF}\\x{370}-\\x{37D}\\x{37F}-\\x{1FFF}\\x{200C}-\\x{200D}\\x{2070}-\\x{218F}\\x{2C00}-\\x{2FEF}\\x{3001}-\\x{D7FF}\\x{F900}-\\x{FDCF}\\x{FDF0}-\\x{FFFD}\\x{10000}-\\x{EFFFF}]";
		$this->NameChar = "[:A-Z_a-z\\-.0-9\\xC0-\\xD6\\xD8-\\xF6\\xF8-\\x{2FF}\\x{370}-\\x{37D}\\x{37F}-\\x{1FFF}\\x{200C}-\\x{200D}\\x{2070}-\\x{218F}\\x{2C00}-\\x{2FEF}\\x{3001}-\\x{D7FF}\\x{F900}-\\x{FDCF}\\x{FDF0}-\\x{FFFD}\\x{10000}-\\x{EFFFF}]";
		$this->Name = "{$this->NameStartChar}{$this->NameChar}*";
		$this->Names = "{$this->Name}(\\x20{$this->Name})*";
		$this->Nmtoken = "{$this->NameChar}+";
		$this->Nmtokens = "{$this->Nmtoken}(\\x20{$this->Nmtoken})*";

		$this->PubidCharBase = "[\\x20\\xD\\xA\\-a-zA-Z0-9()+,./:=?;!*#@\$_%]";
		$this->PubidChar = "[\\x20\\xD\\xA\\-a-zA-Z0-9()+,./:=?;!*#@\$_%']";
		$this->PubidLiteral = "(\"{$this->PubidChar}*\")|('{$this->PubidCharBase}*')";

		$this->CharData = "([^<&]?|(][^<&]+?][^<&]*?>)|(]][^<&]+?>))*";
		$this->Comment = "<!--({$this->CommentChar}|-{$this->CommentChar})*-->";

		$this->PITarget = "{$this->NameStartChar}{$this->NameChar}{3,}|{$this->NameStartCharNonXx}{$this->NameChar}{2}|{$this->NameStartChar}{$this->NameCharNonMm}{$this->NameChar}|{$this->NameStartChar}{$this->NameChar}{$this->NameCharNonLl}";
		$this->PI = "<\\?{$this->PITarget}({$this->S}+(\\?*{$this->Char}+?>*)*)?\\?>";

		$this->CDStart = "<!\\[CDATA\\[";
		$this->CData = "({$this->Char}|]{$this->Char}+?]{$this->Char}*?>|]]{$this->Char}+?>)*";
		$this->CDEnd = "]]>";
		/* $this->CDSect = "{$this->CDStart}{$this->CData}{$this->CDEnd}"; */
		$this->UntilRSBs = "[^\\]]*]([^\\]]+])*]+"; // INVALID (regex depth hack) - uses full char, not $this->Char
		$this->CDSect = "{$this->CDStart}{$this->UntilRSBs}([^\\]>]{$this->UntilRSBs})*>"; // INVALID ^

		$this->Eq = "{$this->S}*={$this->S}*";
		$this->VersionNum = "1.[0-9]+";
		$this->VersionInfo = "{$this->S}+version{$this->Eq}('{$this->VersionNum}'|\"{$this->VersionNum}\")";
		$this->SDDecl = "{$this->S}+standalone{$this->Eq}(('(yes|no)')|(\"(yes|no)\")";

		$this->ETag = "</{$this->Name}{$this->S}*>";

		$this->Mixed = "\\({$this->S}*#PCDATA({$this->S}*\\|{$this->S}*{$this->Name})*{$this->S}*\\)\\*|\\({$this->S}*#PCDATA{$this->S}*\\)";

		$this->StringType = "CDATA";
		$this->TokenizedType = "ID|IDREF|IDREFS|ENTITY|ENTITIES|NMTOKEN|NMTOKENS";
		$this->NotationType = "NOTATION{$this->S}+\\({$this->S}*{$this->Name}({$this->S}*\\|{$this->S}*{$this->Name})*{$this->S}*\\)";
		$this->Enumeration = "\\({$this->S}*{$this->Nmtoken}({$this->S}*\\|{$this->S}*{$this->Nmtoken})*{$this->S}*\\)";
		$this->EnumeratedType = "{$this->NotationType}|{$this->Enumeration}";
		$this->AttType = "{$this->StringType}|{$this->TokenizedType}|{$this->EnumeratedType}";

		$this->CharRef = "&#[0-9]+;|&#x[0-9a-fA-F]+;";
		$this->EntityRef = "&{$this->Name};";
		$this->Reference = "{$this->EntityRef}|{$this->CharRef}";
		$this->PEReference = "%{$this->Name};";

		$this->EntityValue = "(\"([^%&\"]|{$this->PEReference}|{$this->Reference})*\")|('([^%&']|{$this->PEReference}|{$this->Reference})*')";
		$this->AttValue = "(\"([^<&\"]|{$this->Reference})*\")|('([^<&']|{$this->Reference})*')";
		$this->SystemLiteral = "(\"[^\"]*\")|('[^']*')";

		$this->DeclSep = "{$this->PEReference}|{$this->S}+";
		$this->DefaultDecl = "#REQUIRED|#IMPLIED|((#FIXED{$this->S}+)?{$this->AttValue})";

		$this->AttDef = "{$this->S}+{$this->Name}{$this->S}+{$this->AttType}{$this->S}+{$this->DefaultDecl}";
		$this->AttlistDecl = "<!ATTLIST{$this->S}+{$this->Name}({$this->AttDef})*{$this->S}*>";

		$this->EncName = "[A-Za-z][A-Za-z0-9._\\-]*";
		$this->EncodingDecl = "{$this->S}+encoding{$this->Eq}(\"{$this->EncName}\"|'{$this->EncName}')";

		$this->TextDecl = "<\\?xml({$this->VersionInfo})?{$this->EncodingDecl}{$this->S}*\\?>";
		$this->XMLDecl = "<\\?xml{$this->VersionInfo}({$this->EncodingDecl})?{$this->SDDecl}*{$this->S}*\\?>";
		$this->Misc = "{$this->Comment}|{$this->PI}|{$this->S}+";

		$this->ExternalID = "SYSTEM{$this->S}+{$this->SystemLiteral}|PUBLIC{$this->S}+{$this->PubidLiteral}{$this->S}+{$this->SystemLiteral}";
		$this->NDataDecl = "{$this->S}+NDATA{$this->S}+{$this->Name}";
		$this->PublicID = "PUBLIC{$this->S}+{$this->PubidLiteral}";
		$this->NotationDecl = "<!NOTATION{$this->S}+{$this->Name}{$this->S}+({$this->ExternalID}|{$this->PublicID}){$this->S}*>";

		$this->EntityDef = "{$this->EntityValue}|{$this->ExternalID}({$this->NDataDecl})?";
		$this->GEDecl = "<!ENTITY{$this->S}+{$this->Name}{$this->S}+{$this->EntityDef}{$this->S}*>";
		$this->PEDef = "{$this->EntityValue}|{$this->ExternalID}";
		$this->PEDecl = "<!ENTITY{$this->S}+%{$this->S}+{$this->Name}{$this->S}+{$this->PEDef}{$this->S}*>";
		$this->EntityDecl = "{$this->GEDecl}|{$this->PEDecl}";

		$this->_locked = true;
	}

	/**
	 * Creates instance of this class.
	 * @param int $wrapMode expression wrapping mode to be used (XmlRegex::WRAP_*)
	 * @param int $flags flags to be added to wrapped expressions
	 * @return XmlRegex instance
	 */
	public static function getInstance ($wrapMode = null, $flags = null)
	{
		if (!self::$instance)
		{
			self::$instance = new self();
		}
		
		if ($wrapMode !== null)
		{
			self::$instance->setWrapMode($wrapMode);
		}
		else if (!self::$instance->getWrapMode())
		{
			self::$instance->setWrapMode(self::WRAP_NONE);
		}

		if ($flags !== null)
		{
			self::$instance->setFlags($flags);
		}
		
		return self::$instance;
	}

	/**
	 * Makes predefined expressions read-accessible as instance members.
	 * @param $name string expression name
	 * @return string wrapped regexp
	 */
	public function __get ($name)
	{
		if (isset($this->_expressions[$name]))
		{
			return $this->wrap($this->_expressions[$name]);
		}
		return null;
	}

	/**
	 * Allows adding of predefined expressions (only in constructor).
	 * @param $name string name of the expression
	 * @param $value string unwrapped regular expression
	 */
	public function __set ($name, $value)
	{
		if (!$this->_locked)
		{
			$this->_expressions[$name] = $value;
		}
	}

	/**
	 * Gets currently set expression flags.
	 * @return int flags
	 */
	public function getFlags ()
	{
		return $this->_flags;
	}

	/**
	 * Sets expression flags to be added to wrapped expressions.
	 * @param int $flags binary union of applicable flags (XmlRegex::CASE_INSENSITIVE,
	 *		XmlRegex::DOT_MATCH_ALL, XmlRegex::LINE_ANCHORS, XmlRegex::FREE_SPACING_MODE)
	 * @return XmlRegex self
	 */
	public function setFlags ($flags)
	{
		$this->_flags = $flags;
		return $this;
	}

	/**
	 * Gets currently set wrapping mode.
	 * @return int wrapping mode (XmlRegex::WRAP_*)
	 */
	public function getWrapMode ()
	{
		return $this->_wrap;
	}

	/**
	 * Sets wrapping mode to be used when wrapping expressions.
	 * @param int $mode one of XmlRegex::WRAP_* constants
	 * @return XmlRegex self
	 */
	public function setWrapMode ($mode)
	{
		$this->_wrap = $mode;
		return $this;
	}

	/**
	 * Wraps supplied bare regular expression.
	 * @param string $expr bare regular expression (not wrapped in any way)
	 * @param int $mode set to override currently set wrapping mode
	 * @param int $flags set to override currently set flags
	 * @return string wrapped & flagged regular expression (or just @c $expr if
	 *		XmlRegex::WRAP_NONE mode is used)
	 */
	public function wrap ($expr, $mode = null, $flags = null)
	{
		if ($mode === null) $mode = $this->getWrapMode();
		if ($flags === null) $flags = $this->getFlags();
		if ($mode == self::WRAP_PERL)
		{
			$delimiter = '/';
			$code = ord($delimiter);
			while (strstr($expr, $delimiter))
			{
				--$code;
				if (chr($code) == ' ') continue;
				$delimiter = chr($code);
			}
			$ret = "{$delimiter}{$expr}{$delimiter}u"
				. (($flags & self::CASE_INSENSITIVE) ? 'i' : '')
				. (($flags & self::DOT_MATCH_ALL) ? 's' : '')
				. (($flags & self::LINE_ANCHORS) ? 'm' : '')
				. (($flags & self::FREE_SPACING_MODE) ? 'x' : '');
			return $ret;
		}
		return "({$expr})";
	}
}

