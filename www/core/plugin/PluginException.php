<?php

namespace asm\plugin;
use Exception;

/**
 * Exception to be thrown by @ref Plugin "plugins".
 *
 * Use PluginCodeException and PluginUseException for cases when it's clear that
 * the plugin has been incorrectly written or is being improperly used.
 */
class PluginException extends Exception
{
	
}

