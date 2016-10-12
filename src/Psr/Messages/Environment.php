<?php

namespace Almendra\Http\Psr\Messages;

use Almendra\Http\Collection;
use Almendra\Http\Psr\Interfaces\EnvironmentInterface;

/**
 * Represents the message environment.
 *
 * @package Almendra\Http
 */
class Environment extends Collection implements EnvironmentInterface
{
    /**
     * Create mock environment
     *
     * @param  array $userData Array of custom environment keys and values
     * @return array
     */
    public static function mock(array $userData = [])
    {
        $data = array_merge([
            'SERVER_PROTOCOL'      => 'HTTP/1.1',
            'REQUEST_METHOD'       => 'GET',
            'SCRIPT_NAME'          => '',
            'REQUEST_URI'          => '',
            'QUERY_STRING'         => '',
            'SERVER_NAME'          => 'localhost',
            'SERVER_PORT'          => 8000,
            'HTTP_HOST'            => 'localhost',
            'HTTP_ACCEPT'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'HTTP_ACCEPT_LANGUAGE' => 'en-US,en;q=0.8',
            'HTTP_ACCEPT_CHARSET'  => 'ISO-8859-1,utf-8;q=0.7,*;q=0.3',
            'HTTP_USER_AGENT'      => 'Gate Framework',
            'REMOTE_ADDR'          => '127.0.0.1',
            'REQUEST_TIME'         => time(),
            'REQUEST_TIME_FLOAT'   => microtime(true),
        ], $userData);

        return $data;
    }

    /**
     * Create environment from the global variables
     *
     * @param  array $userData Array of custom environment keys and values
     * @return array
     */
    public static function init(array $userData = [])
    {
        // @todo determine assignments individually
        $data = array_merge([
            'SERVER_PROTOCOL'      => 'HTTP/1.1',
            'REQUEST_METHOD'       => $_SERVER['REQUEST_METHOD'],
            'SCRIPT_NAME'          => $_SERVER['SCRIPT_NAME'],
            'REQUEST_URI'          => $_SERVER['REQUEST_URI'],
            // 'QUERY_STRING'         => URIHelper::getQueryParams($_SERVER['REQUEST_URI']),
            'QUERY_STRING'         => self::getServerValue('QUERY_STRING'),
            'SERVER_NAME'          => $_SERVER['SERVER_NAME'],
            'SERVER_PORT'          => $_SERVER['SERVER_PORT'],
            'HTTP_HOST'            => $_SERVER['HTTP_HOST'],
            'HTTP_ACCEPT'          => $_SERVER['HTTP_ACCEPT'],
            'HTTP_ACCEPT_LANGUAGE' => self::getServerValue('HTTP_ACCEPT_LANGUAGE', 'en'),
            'HTTP_ACCEPT_CHARSET'  => self::getServerValue('HTTP_ACCEPT_CHARSET', 'ISO-8859-1,utf-8;q=0.7,*;q=0.3'),
            'HTTP_USER_AGENT'      => $_SERVER['HTTP_USER_AGENT'],
            'REMOTE_ADDR'          => self::getServerValue('REMOTE_ADDR'),
            'REQUEST_TIME'         => time(),
            'REQUEST_TIME_FLOAT'   => microtime(true),
        ], $userData);

        return $data;
    }

    public static function getServerValue($value, $default = '')
    {
        if (array_key_exists($value, $_SERVER)) {
            return $_SERVER[$value];
        }

        return $default;
    }
}
