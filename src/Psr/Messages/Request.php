<?php

namespace Almendra\Http\Psr\Messages;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\Environment;

use Almendra\Http\Server;

use Almendra\Http\Requests\Field;

/**
 * Represents an incomming request.
 * Server specific methods are left to ServerRequest.
 *
 * @package Almendra\Http
 * @author 	author 	<email>
 */
class Request extends Message implements RequestInterface
{
    protected $_target;
    protected $_method;
    protected $_Uri;
    protected $_params = [];

    protected $_contentType;

    protected $_scriptName;
    protected $_queryString;
    protected $_serverName;
    protected $_serverPort;
    protected $_host;
    protected $_accept;
    protected $_acceptLanguage;
    protected $_acceptCharset;
    protected $_userAgent;
    protected $_remoteAddr;
    protected $_time;
    protected $_timeFloat;

    protected $_fields = [];


    public function __construct(UriInterface $uri = null)
    {
        if (isset($uri) && $uri !== null) {
            $this -> setUri($uri);
        }
    }

    public function createFromEnvironment($params)
    {
        // repart values among the properties
        foreach ($params as $param => $value) {
            $this -> _params[$param] = $value; // @tmp for debugging
            $this -> assign($param, $value);
        }

        $this -> fillFields($this -> getMethod());

        return $this;
    }

    /**
     * Assign the environmental values to the proper members
     *
     * @param string $key 			Environmental value key
     * @param string $value 		Environmental value value
     * @return boolean 				false if failure
     */
    public function assign($key, $value)
    {
        switch ($key) {
            case 'SCRIPT_NAME':
                $this -> _scriptName = $value;
                break;

            case 'REQUEST_METHOD':
                $this -> setMethod($value);
                break;

            case 'REQUEST_URI':
                $this -> setUri(new Uri($value));
                break;

            case 'SERVER_PROTOCOL':
                $this -> _protocolVersion = $value;
                break;

            case 'SCRIPT_NAME':
                $this -> _scriptName = $value;
                break;

            case 'QUERY_STRING':
                $this -> _queryString = $value;
                break;

            case 'SERVER_NAME':
                $this -> _serverName = $value;
                break;

            case 'SERVER_PORT':
                $this -> _serverPort = $value;
                break;

            case 'HTTP_HOST':
                $this -> _host = $value;
                break;

            case 'HTTP_ACCEPT':
                $this -> _accept = $value;
                break;

            case 'HTTP_ACCEPT_LANGUAGE':
                $this -> _acceptLanguage = $value;
                break;

            case 'HTTP_ACCEPT_CHARSET':
                $this -> _acceptCharset = $value;
                break;

            case 'HTTP_USER_AGENT':
                $this -> _userAgent = $value;
                break;

            case 'REMOTE_ADDR':
                $this -> _remoteAddr = $value;
                break;

            case 'REQUEST_TIME':
                $this -> _time = $value;
                break;

            case 'REQUEST_TIME_FLOAT':
                $this -> _timeFloat = $value;
                break;

            case 'CONTENT_TYPE':
                $this -> _contentType = $value;
                break;
            
            // @todo remaining cases

            default:
                return false;
                break;
        }

        return true;
    }


    public function getRequestTarget()
    {
        // retrieve the target
        $target = ('' != $this -> getUri() -> getPath()) ?
            $this -> getUri() -> getPath() :
            '/';

        // attach the query parameters
        $params = $this -> getUri() -> getQuery();
        if ('' != $params) {
            $target .= '?' . $params;
        }

        return $target;
    }

    public function withRequestTarget($target)
    {
        $clone = clone $this;
        $clone -> setTarget($target);

        return $clone;
    }

    public function setTarget($target)
    {
        $this -> _target = $target;
    }

    public function getMethod()
    {
        return $this -> _method;
    }

    public function setMethod($method)
    {
        $this -> _method = $method;
    }

    public function withMethod($method)
    {
        $clone = clone $this;
        $clone -> setMethod($method);

        return $clone;
    }

    /**
     * Retrieves the request's URI.
     *
     * @return Psr\Http\Message\UriInterface
     */
    public function getUri()
    {
        return new Uri($this -> _Uri);
    }

    public function setUri(UriInterface $uri)
    {
        $this -> _Uri = $uri -> __toString();
    }

    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $clone = clone $this;
        $clone -> setUri($uri);

        return $clone;
    }

    public function all()
    {
        return $this -> _fields;
    }

    public function get($name)
    {
        if (isset($this -> _fields['get'][$name])) {
            return $this -> _fields['get'][$name];
        }

        return null;
    }

    public function post($name)
    {
        if (isset($this -> _fields['post'][$name])) {
            return $this -> _fields['post'][$name];
        }

        return null;
    }

    public function put($name)
    {
        if (isset($this -> _fields['put'][$name])) {
            return $this -> _fields['put'][$name];
        }

        return null;
    }

    public function delete($name)
    {
        if (isset($this -> _fields['delete'][$name])) {
            return $this -> _fields['delete'][$name];
        }

        return null;
    }

    public function files($name)
    {
        if (isset($this -> _fields['files'][$name])) {
            return $this -> _fields['files'][$name];
        }

        return null;
    }

    protected function fillFields($method)
    {
        $server = new Server;
        $fields = [];

        $fields['files'] = $server -> files() -> all();
        switch ($method) {
            case 'GET':
                $fields['get'] = $server -> get() -> all();
                break;

            case 'POST':
                $fields['post'] = $server -> post() -> all();
                break;

            // @todo fake a PUT request
            // case 'PUT':
            // 	$fields['put'] = $server -> put() -> all();
            // 	break;

            default:
                throw new \InvalidMethodException("No valid method provided.");
                break;
        }

        $this -> _fields = $fields;
    }




    public function dummyAll()
    {
        return [
            'value1' => 1,
            'value2' => 100,
            ];
    }
}
