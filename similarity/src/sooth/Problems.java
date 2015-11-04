package sooth;

/**
 * Contains constants corresponding to plugin identifiers.
 *
 * Each plugin must contain an identifier in its manifest file. These identifiers are then used by this similarity module
 * to differentiate between plugins. This is more useful than using the plugin name or the database ID because these
 * might be different on different installations of the XML Check system, whereas identifiers will be the same.
 */
public class Problems {
    /**
     * Identifier of the XML/DTD plugin.
     */
    public static final String HW1_DTD = "DTD";
    /**
     * Identifier of the DOM/SAX plugin.
     */
    public static final String HW2_DOMSAX = "DOMSAX";
    /**
     * Identifier of the XPath plugin.
     */
    public static final String HW3_XPATH = "XPATH";
    /**
     * Identifier of the XML Schema (XSD) plugin.
     */
    public static final String HW4_SCHEMA = "SCHEMA";
    /**
     * Identifier of the XQuery plugin.
     */
    public static final String HW5_XQUERY = "XQUERY";
    /**
     * Identifier of the XSLT plugin.
     */
    public static final String HW6_XSLT = "XSLT";
}
