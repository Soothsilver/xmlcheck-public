<?php

namespace asm\core;
use asm\core\lang\StringID;
use asm\utils\Filesystem, asm\utils\Compression;

/**
 * @ingroup requests
 * Stores uploaded plugin file and adds new plugin entry to database.
 * @n @b Requirements: User::addPlugins privilege
 * @n @b Arguments:
 * @li @c name plugin name
 * @li @c plugin ZIP archive with plugin files and manifest
 */
final class AddPlugin extends DataScript
{
	protected function body ()
	{
		if (!$this->userHasPrivileges(User::pluginsAdd))
			return false;

		$inputs = array(
			'name' => array(
				'isName',
				'isNotEmpty'
			),
		);
		if (!$this->isInputValid($inputs))
			return false;

		$name = $this->getParams('name');

		$existingPluginsWithSameName = Repositories::getRepository(Repositories::Plugin)->findBy(['name' => $name]);
		if (count($existingPluginsWithSameName) > 0)
			return $this->death(StringID::PluginNameAlreadyExists);

		$pluginFile = $this->getUploadedFile('plugin');

		if (!$pluginFile)
			return false;


		$pluginFolder = Config::get('paths', 'plugins') . $name;

		if (file_exists($pluginFolder))
			return $this->death(StringID::PluginFolderAlreadyExists);

		if (!Filesystem::createDir($pluginFolder))
			return $this->death(StringID::FileSystemError);

		if (!Compression::unzip($pluginFile, $pluginFolder))
		{
			$this->death(StringID::UnzipUnsuccessful);
			goto cleanup_error;
		}

		$manifestFile = $pluginFolder . DIRECTORY_SEPARATOR . 'manifest.xml';
		$manifest = null;
		if (!($manifest = $this->parsePluginManifest($manifestFile)))
		{
			$this->death(StringID::BadlyFormedPlugin);
			goto cleanup_error;
		}

		if (!file_exists($pluginFolder . DIRECTORY_SEPARATOR . $manifest['mainFile']))
		{
			$this->death(StringID::BadlyFormedPlugin);
			goto cleanup_error;
		}

		$plugin = new \Plugin();
		$plugin->setIdentifier($manifest['identifier']);
		$plugin->setDescription($manifest['description']);
		$plugin->setConfig($manifest['arguments']);
		$plugin->setMainfile($name . '/' . $manifest['mainFile']);
		$plugin->setName($name);
		$plugin->setType($manifest['type']);
		Repositories::persistAndFlush($plugin);

		Filesystem::removeFile($pluginFile);
		return true;

cleanup_error:
		Filesystem::removeDir($pluginFolder);
		Filesystem::removeFile($pluginFile);
		return false;
	}

	protected function parsePluginManifest ($manifestFile)
	{
		$manifestString = file_get_contents($manifestFile);
		$manifestXml = @simplexml_load_string($manifestString);
		if (!$manifestXml)
		{
			return false;
		}

		/** @noinspection PhpUndefinedFieldInspection */
		$manifest = array(
			'type' => trim($manifestXml->type),
			'description' => trim($manifestXml->description),
			'mainFile' => trim($manifestXml->mainFile),
			'identifier' => trim($manifestXml->identifier)
		);
		$arguments = array();
		/** @noinspection PhpUndefinedFieldInspection */
		foreach ($manifestXml->arguments->children() as $argument)
		{
			$arguments[] = trim($argument);
		}

		$manifest['arguments'] = implode(';', $arguments);

		return $manifest;
	}
}

