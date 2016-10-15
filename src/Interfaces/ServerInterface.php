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

    public function get();

    public function post();

    public function files();

    public function all();

    //
}
