<?php

namespace asm\utils;
require_once(__DIR__ . '/PasswordHash.php');

/**
 * Contains static functions that handle hashing passwords.
 */
class Security {
    /**
     * This string in the user's encryptionType means that his password is hashed using the PHPASS library.
     */
    const HASHTYPE_PHPASS = 'phpass';
    /**
     * This string in the user's encryptionType means that his password is hashed using the MD5 algorithm.
     */
    const HASHTYPE_MD5 = 'md5';
    /**
     * Creates a hash of the password given using the specified encryption method.
     * @param $plainPassword string password to hash
     * @param $encryptionType string md5 or phpass
     * @return string the hash
     */
    public static function hash($plainPassword, $encryptionType = self::HASHTYPE_PHPASS)
    {
        if ($encryptionType === self::HASHTYPE_MD5)
        {
            return md5($plainPassword);
        }
        else if ($encryptionType === self::HASHTYPE_PHPASS)
        {
            $passwordHash = new \PasswordHash(8, false);
            return $passwordHash->HashPassword($plainPassword);
        }
        else
        {
            throw new \InvalidArgumentException("Only 'md5' and 'phpass' encryption methods are supported.");
        }
    }

    /**
     * Hashes the given password and compares it to the given hash using the specified method.
     *
     * Returns true if the hashes match.
     *
     * @param $incomingPassword string the password typed just now by the user
     * @param $databaseHash string the hash in the database
     * @param $encryptionType string md5 or phpass
     * @return bool do the passwords match?
     */
    public static function check($incomingPassword, $databaseHash, $encryptionType = self::HASHTYPE_PHPASS)
    {
        if ($encryptionType === self::HASHTYPE_MD5)
        {
            return md5($incomingPassword) === $databaseHash;
        }
        else if ($encryptionType === self::HASHTYPE_PHPASS)
        {
            $passwordHash = new \PasswordHash(8, false);
            return $passwordHash->CheckPassword($incomingPassword, $databaseHash);
        }
        else
        {
            throw new \InvalidArgumentException("Only 'md5' and 'phpass' encryption methods are supported.");
        }
    }
} 