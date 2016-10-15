<?php

namespace Almendra\Http\Psr\Messages;

use Almendra\Http\Collection;
use Almendra\Http\Interfaces\EnvironmentInterface;
use Almendra\Http\Server;

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
        $data = array_merge([
            'SERVER_PROTOCOL'      => 'HTTP/1.1',
            'REQUEST_METHOD'       => Server::getValue('REQUEST_METHOD'),
            'SCRIPT_NAME'          => Server::getValue('SCRIPT_NAME'),
            'REQUEST_URI'          => Server::getValue('REQUEST_URI'),
            'QUERY_STRING'         => Server::getValue('QUERY_STRING'),
            'SERVER_NAME'          => Server::getValue('SERVER_NAME'),
            'SERVER_PORT'          => Server::getValue('SERVER_PORT'),
            'HTTP_HOST'            => Server::getValue('HTTP_HOST'),
            'HTTP_ACCEPT'          => Server::getValue('HTTP_ACCEPT'),
            'HTTP_ACCEPT_LANGUAGE' => Server::getValue('HTTP_ACCEPT_LANGUAGE', 'en'),
            'HTTP_ACCEPT_CHARSET'  => Server::getValue('HTTP_ACCEPT_CHARSET', 'ISO-8859-1,utf-8;q=0.7,*;q=0.3'),
            'HTTP_USER_AGENT'      => Server::getValue('HTTP_USER_AGENT'),
            'REMOTE_ADDR'          => Server::getValue('REMOTE_ADDR'),
            'REQUEST_TIME'         => time(),
            'REQUEST_TIME_FLOAT'   => microtime(true),
        ], $userData);

        return $data;
    }
}
