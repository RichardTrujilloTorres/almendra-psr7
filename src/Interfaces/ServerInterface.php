<?php

namespace Almendra\Http\Interfaces;

interface ServerInterface
{
    /**
     * Retrieves a value defined in the superglobal $_SERVER.
     *
     * @param string $value         The key's name.
     * @return string|mixed
     */
    public static function getValue($value, $default = '');

    /**
     * Retrieves all values defined in the superglobal $_SERVER.
     *
     * @param string $value         The key's name.
     * @return string|mixed
     */
    public static function getValues();

    /**
     * Returns a value from the $_GET superglobal.
     * Null if none exists.
     *
     * @param string $name         The value's name
     * @return mixed                 
     */
    public static function get($name);

    /**
     * Returns all values from the $_GET superglobal.
     * Null if none exists.
     *
     * @param string $name         
     * @return mixed                 
     */
    public static function gets(array $values = null);

    public static function post($name);

    public static function posts(array $values = null);

    public static function file($name);

    public static function files(array $values = null);

    //
}
