<?php

use asm\core\Config,
    asm\core\Core,
    asm\utils\ErrorHandler,
	asm\core\UiResponse,
    asm\core\Error;


/**
 * @file
 * Handles core requests coming from UI.
 *
 * 1. Starts session.
 * 2. Starts the Composer autoloader.
 * 3. Loads configuration from the config.ini and internal.ini files.
 * 4. Activates the custom error handler.
 * 5. Calls Core::handleUiRequest with data from user.
 */

// Session is used to keep track of logged in user, and is used for file uploads.
session_start();

// Load up the Composer-generated autoloader. All PHP classes are loaded using this autoloader.
require_once(__DIR__ . "/../vendor/autoload.php");

// Load configuration from the "config.ini" file.
Config::init(__DIR__ . '/config.ini', __DIR__ . '/internal.ini');

// If ever an exception occurs or a PHP error occurs, log it and send it to the user.
ErrorHandler::register();
ErrorHandler::bind(['asm\core\Core', 'logException']);
ErrorHandler::bind(function (Exception $e) {
	Core::sendUiResponse(UiResponse::create([], [
			Error::create(Error::levelFatal, $e->getMessage() . "[details: code " . $e->getCode() . ", file " . $e->getFile() . ", line " . $e->getLine() . ", trace: \n" . $e->getTraceAsString() . "]",
                \asm\core\lang\Language::get(\asm\core\lang\StringID::ServerSideRuntimeError))]));
});

// Process the AJAX request.
// Usually, the Javascript part of XML Check sends a POST request but in some special cases, a GET request is needed.
Core::handleUiRequest(empty($_POST) ? $_GET : $_POST, $_FILES);
