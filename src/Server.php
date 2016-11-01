<?php

namespace Almendra\Http;

use Almendra\Http\Interfaces\ServerInterface;

/**
 * A wrapper for the superglobals $_SERVER, $_GET and $_POST.
 *
 * @package Almendra\PSR7    
 * @author     Richard Trujillo Torres     <richard.trujillo.torres@gmail.com>
 */
class Server implements ServerInterface
{
    /**
     * Retrieves a value defined in the superglobal $_SERVER.
     *
     * @param string $value 		The key's name.
     * @return string|mixed
     */
    public static function getValue($value, $default = '')
    {
        if (array_key_exists($value, $_SERVER)) {
            return $_SERVER[$value];
        }

        return $default;
    }

    /**
     * Retrieves all values defined in the superglobal $_SERVER.
     *
     * @return string|mixed
     */
    public static function getValues()
    {
        return $_SERVER;
    }


    /**
     * Returns a value from the $_GET superglobal.
     * Null if none exists.
     *
     * @param string $name         The value's name
     * @return mixed                 
     */
    public static function get($name)
    {
        if (isset($_GET[$name])) {
            return $_GET[$name];
        }

        return null;
    }

    /**
     * Returns all values from the $_GET superglobal.
     * Null if none exists.
     *
     * @param array $values         An array containing value names to be retrieved         
     * @return mixed                 
     */
    public static function gets(array $values = null)
    {
        $fields = [];
        if (isset($values) && null !== $values) {
            foreach ($values as $name) {
                $fields[] = self::get($name);
            }

            return $fields;
        } 

        return isset($_GET) ? $_GET : null;
    }

    /**
     * Returns a value from the $_POST superglobal.
     * Null if none exists.
     *
     * @param string $name         The value's name
     * @return mixed                 
     */
    public static function post($name)
    {
        if (isset($_POST[$name])) {
            return $_POST[$name];
        }

        return null;
    }

    /**
     * Returns all values from the $_POST superglobal.
     * Null if none exists.
     *
     * @param array $values         
     * @return mixed                 
     */
    public static function posts(array $values = null)
    {
        $fields = [];
        if (isset($values) && null !== $values) {
            foreach ($values as $name) {
                $fields[] = self::post($name);
            }

            return $fields;
        } 

        return isset($_POST) ? $_POST : null;
    }

    /**
     * Returns a value from the $_FILES superglobal.
     * Null if none exists.
     *
     * @param string $name         The value's name
     * @return mixed                 
     */
    public static function file($name)
    {
        if (isset($_FILES[$name])) {
            return $_FILES[$name];
        }

        return null;
    }

    /**
     * Returns all values from the $_FILES superglobal.
     * Null if none exists.
     *
     * @param array $values         An array containing value names to be retrieved    
     * @return mixed                 
     */
    public static function files(array $values = null)
    {
        $fields = [];
        if (isset($values) && null !== $values) {
            foreach ($values as $name) {
                $fields[] = self::file($name);
            }

            return $fields;
        } 

        return isset($_FILES) ? $_FILES : null;
    }

}
