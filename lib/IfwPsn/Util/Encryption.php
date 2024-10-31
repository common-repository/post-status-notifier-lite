<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Encryption helper
 *
 * @author    Timo Reith <timo@ifeelweb.de>
 * @version   $Id: Encryption.php 3065031 2024-04-04 20:25:44Z worschtebrot $
 * @package   
 */ 
class IfwPsn_Util_Encryption
{
    /**
     * @param $string
     * @param $salt
     * @param string $preferredCipher
     * @param string $initVector
     * @return string
     */
    public static function encrypt($string, $salt, $preferredCipher = 'AES128', $initVector = 'emptyemptyemptye')
    {
        if (self::isOpenSSL()) {
            $encrypted_string = @openssl_encrypt($string, self::getOpenSSLMethod($preferredCipher), $salt, 0, $initVector);
        } else {
            $encrypted_string = base64_encode($string);
        }

        if (is_string($encrypted_string)) {
            $encrypted_string = str_replace("\0", "", $encrypted_string);
        }

        return $encrypted_string;
    }

    /**
     * @param $string
     * @param $salt
     * @param string $preferredCipher
     * @param string $initVector
     * @return string
     */
    public static function decrypt($string, $salt, $preferredCipher = 'AES128', $initVector = 'emptyemptyemptye')
    {
        if (self::isOpenSSL()) {
            $decrypted_string = @openssl_decrypt($string, self::getOpenSSLMethod($preferredCipher), $salt, 0, $initVector);
        } else {
            $decrypted_string = base64_decode($string);
        }

        if (is_string($decrypted_string)) {
            $decrypted_string = str_replace("\0", "", $decrypted_string);
        }

        return $decrypted_string;
    }

    /**
     * @return bool
     * @deprecated
     */
    public static function isMcrypt()
    {
        return extension_loaded('mcrypt');
    }

    /**
     * @return bool
     */
    public static function isOpenSSL()
    {
        return function_exists('openssl_decrypt') && function_exists('openssl_encrypt');
    }

    /**
     * @param string $preferredCipher
     * @return mixed|string
     */
    public static function getOpenSSLMethod($preferredCipher = 'AES128')
    {
        $ciphers = openssl_get_cipher_methods(true);

        if (in_array($preferredCipher, $ciphers)) {
            return $preferredCipher;
        }

        foreach (array('AES128', 'AES-128-CBC', 'AES-128-CBC-HMAC-SHA1', 'AES192', 'AES-192-CBC', 'AES256', 'AES-256-CBC-HMAC-SHA1') as $cipher) {
            if (in_array($cipher, $ciphers)) {
                return $cipher;
            }
        }

        return $ciphers[0];
    }

    /**
     * @param $str
     * @return bool
     */
    public static function isEncryptedString($str)
    {
        if ( base64_encode(base64_decode($str, true)) === $str){
            return true;
        }
        return false;
    }
}
