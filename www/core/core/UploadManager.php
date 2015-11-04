<?php

namespace asm\core;
use asm\utils\StringUtils;

/**
 * Manager of pre-uploaded files @singleton.
 *
 * Can be used to store uploaded file and return its unique ID. File can later be
 * retrieved by its ID and used as needed (or moved to permanent storage). This
 * allows for "pre-uploading" of files (files are uploaded as soon as they are
 * selected in GUI and their IDs are returned to be sent with the rest of the form
 * later).
 */
class UploadManager
{
	/// @name Error codes
	//@{
	const invalidFileData	= 1;
	const fileUploadError	= 2;
	const fileMoveError		= 3;
	const idNotSet				= 4;
	const fileNotFound		= 5;
	const fileRemovalError	= 6;
	//@}

	const sessionSpace = 'upload';	///< data is stored in $_SESSION under this key

	private static $instance;	///< singleton instance

	protected $storageFolder = null;	///< folder in which uploaded files are stored
	protected $filePrefix = '';	///< prefix for filenames

	/**
	 * [Creates and] returns singleton instance.
	 * @return UploadManager this singleton class' instance
	 */
	public static function instance ()
	{
		if (!self::$instance)
		{
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Initializes @ref $storageFolder and @ref $filePrefix.
	 */
	protected function __construct ()
	{
		if (!session_id())
		{
			session_start();
		}

		/// @ref $storageFolder is retrieved from Config.
		$this->storageFolder = Config::get('paths', 'temp');

		/// @ref $filePrefix is set to username if user is logged in.
		$user = User::instance();
		if ($user->isLogged()) {
			$this->filePrefix = $user->getName() . '-';
		}
	}

	/**
	 * Creates new unique ID for file.
	 * @return string new unique ID (not currently used)
	 */
	protected function generateUniqueId ()
	{
		do
		{
			$id = StringUtils::randomString(10);
		} while (isset($_SESSION[UploadManager::sessionSpace][$id])
				|| file_exists($this->getFilenameFromId($id)));
		return $id;
	}

	/**
	 * Creates filename from supplied ID.
	 * @param string $id
	 * @return string filename for supplied ID
	 * @see generateUniqueId()
	 */
	protected function getFilenameFromId ($id)
	{
		return $this->storageFolder . $this->filePrefix . time() . '-' . $id;
	}

	/**
	 * Stores uploaded file in designated storage folder.
	 * @param array $file uploaded file properties (from $_FILES[\<name\>])
	 * @param[out] int &$error error code in case of failure
	 * @return mixed file ID if stored successfully, or false
	 * @see retrieve()
	 * @see remove()
	 */
	public function store ($file, &$error)
	{
		if (!isset($file['name']) || !isset($file['type']) || !isset($file['tmp_name'])
				|| !isset($file['error']))
		{
			$error = self::invalidFileData;
			return false;
		}
		
		if ($file['error'] != UPLOAD_ERR_OK)
		{
			$error = self::fileUploadError;
			return false;
		}
		
		$id = $this->generateUniqueId();
		$filename = $this->getFilenameFromId($id);
		$uploaded = @move_uploaded_file($file['tmp_name'], $filename);
		if (!$uploaded)
		{
			$error = self::fileMoveError;
			return false;
		}
		
		$_SESSION[UploadManager::sessionSpace][$id] = array(
			'name' => $file['name'],
			'type' => $file['type'],
			'path' => realpath($filename)
		);
		return $id;
	}

	/**
	 * Retrieve stored file with supplied ID [and moves it to supplied destination].
	 * @param string $id file ID
	 * @param string $destination rename/move file to this name
	 * @return mixed array with file properties {'name', 'type', 'path'} or error code in case of error
	 */
	public function retrieve ($id, $destination = null)
	{
		if (!isset($_SESSION[UploadManager::sessionSpace][$id]))
		{
			return self::idNotSet;
		}
		if (!file_exists($_SESSION[UploadManager::sessionSpace][$id]['path']))
		{
			return self::fileNotFound;
		}
		$entry = $_SESSION[UploadManager::sessionSpace][$id];
		unset($_SESSION[UploadManager::sessionSpace][$id]);
		if (($destination != null) && (file_exists(dirname($destination))))
		{
			$moved = @rename($entry['path'], $destination);
			if (!$moved)
			{
				@unlink($entry['path']);
				return self::fileMoveError;
			}
			$entry['path'] = $destination;
		}
		return $entry;
	}

	/**
	 * Deletes file with supplied ID from storage or all files if no ID is supplied.
	 * @param string $id file ID
	 * @return mixed true in case of success, UploadManager::fileRemovalError in case
	 * of failure and false if entry with supplied ID doesn't exist
	 */
	public function remove ($id = null)
	{
		if (!isset($_SESSION[UploadManager::sessionSpace])
				|| (($id !== null) && !isset($_SESSION[UploadManager::sessionSpace][$id])))
		{
			return false;
		}
		
		$fileIds = ($id !== null) ? $id : array_keys($_SESSION[UploadManager::sessionSpace]);
		$ret = true;
		foreach ($fileIds as $fileId)
		{
			$filename = $_SESSION[UploadManager::sessionSpace][$fileId]['path'];
			if (file_exists($filename))
			{
				$removed = @unlink($filename);
				if (!$removed)
				{
					$ret = self::fileRemovalError;
				}
			}
			unset($_SESSION[UploadManager::sessionSpace][$fileId]);
		}
		return $ret;
	}
}

