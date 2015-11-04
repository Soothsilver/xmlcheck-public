<?php

namespace asm\core;

use asm\core\lang\Language;
use asm\core\lang\StringID;
use asm\utils\Filesystem;

/**
 * @ingroup requests
 * Reloads information about all plugins from their manifests. The information reloaded is the plugin name, description,
 * unique identifier and argument number and descriptions.
 *
 * Only plugins that are already in the database are reloaded. This request is invoked from "Other Administration"
 * in the main menu (only administrators have access to it).
 */
final class ReloadManifests extends DataScript
{
	protected function body ()
	{
		$plugins = Repositories::getRepository(Repositories::Plugin)->findAll();
		$errors = [];
		foreach ($plugins as $plugin) {
				/** @var $plugin \Plugin */
				$dbPhpFile = $plugin->getMainfile();
				$dbDescription = $plugin->getDescription();
				$dbIdentifier = $plugin->getIdentifier();
				$pluginDirectory = $this->getMainDirectory($dbPhpFile);
				if ($pluginDirectory === false) {
					$errors[] = $plugin->getName() . ": " . Language::get(StringID::ReloadManifests_InvalidFolder);
					continue;
				}
				$manifestFile = Filesystem::combinePaths(
					Config::get('paths', 'plugins'),
					$pluginDirectory,
					"manifest.xml");
				$xml = new \DOMDocument();
				$success = $xml->load(realpath($manifestFile));
				if ($success === false) {
					$errors[] = $plugin->getName() . ": " . Language::get(StringID::ReloadManifests_MalformedXmlOrFileMissing);
					continue;
				}

				$fileDescription = $xml->getElementsByTagName('description')->item(0);
				$fileArguments = $xml->getElementsByTagName('argument');
				$fileIdentifier = $xml->getElementsByTagName('identifier')->item(0);
				$fileArgumentsArray = [];
				for ($i = 0; $i < $fileArguments->length; $i++) {
					$fileArgumentsArray[] = trim($fileArguments->item($i)->nodeValue);
				}
				$fileArgumentsString = implode(';', $fileArgumentsArray);
				if ($dbDescription !== trim($fileDescription->nodeValue)) {
					$errors[] = $plugin->getName() . ": " . Language::get(StringID::ReloadManifests_DescriptionMismatch);
					$plugin->setDescription(trim($fileDescription->nodeValue));
					Repositories::persist($plugin);
				}
				if ($dbIdentifier !== trim($fileIdentifier->nodeValue)) {
					$errors[] = $plugin->getName() . ": " . Language::get(StringID::ReloadManifests_IdentifierMismatch);
					$plugin->setIdentifier(trim($fileIdentifier->nodeValue));
					Repositories::persist($plugin);
				};
				if ($plugin->getConfig() !== $fileArgumentsString) {
					$errors[] = $plugin->getName() . ": " . Language::get(StringID::ReloadManifests_ArgumentsMismatch);
					$plugin->setConfig($fileArgumentsString);
					Repositories::persist($plugin);
				}
		}
		Repositories::flushAll();

		if (count($errors) === 0) {
			$this->addOutput("text", Language::get(StringID::ReloadManifests_DatabaseCorrespondsToManifests));
		}
		else {
			$this->addOutput("text", implode('<br>', $errors));
		}
		return true;
	}
	private function getMainDirectory($path)
	{
		$fragments = explode('/', $path);
		if (count($fragments) > 1 && strpos($fragments[0], "\\") === false)
		{
			return $fragments[0];
		}
		$fragments = explode("\\", $path);
		if (count($fragments) > 1 && strpos($fragments[0], "/") === false)
		{
			return $fragments[0];
		}
		return false;
	}
}

