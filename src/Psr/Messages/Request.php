<?php

namespace Almendra\Http\Psr\Messages;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Almendra\Http\Psr\Messages\Environment;

use Almendra\Http\Server;

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

    public function createFromEnvironment(Environment $environment)
    {
        $params = $environment::init([]);

        // repart values among the properties
        foreach ($params as $param => $value) {
            // $this -> _params[$param] = $value; // @tmp for debugging
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

            default:
                return false;
                break;
        }

        return true;
    }

    /**
     * Retrieves the message's request target.
     *
     * Retrieves the message's request-target either as it will appear (for
     * clients), as it appeared at request (for servers), or as it was
     * specified for the instance (see withRequestTarget()).
     *
     * In most cases, this will be the origin-form of the composed URI,
     * unless a value was provided to the concrete implementation (see
     * withRequestTarget() below).
     *
     * If no URI is available, and no request-target has been specifically
     * provided, this method MUST return the string "/".
     *
     * @return string
     */
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

    /**
     * Return an instance with the specific request-target.
     *
     * If the request needs a non-origin-form request-target — e.g., for
     * specifying an absolute-form, authority-form, or asterisk-form —
     * this method may be used to create an instance with the specified
     * request-target, verbatim.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request target.
     *
     * @link http://tools.ietf.org/html/rfc7230#section-5.3 (for the various
     *     request-target forms allowed in request messages)
     * @param mixed $requestTarget
     * @return static
     */
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

    /**
     * Retrieves the HTTP method of the request.
     *
     * @return string Returns the request method.
     */
    public function getMethod()
    {
        return $this -> _method;
    }

    public function setMethod($method)
    {
        $this -> _method = $method;
    }

    /**
     * Return an instance with the provided HTTP method.
     *
     * While HTTP method names are typically all uppercase characters, HTTP
     * method names are case-sensitive and thus implementations SHOULD NOT
     * modify the given string.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request method.
     *
     * @param string $method Case-sensitive method.
     * @return static
     * @throws \InvalidArgumentException for invalid HTTP methods.
     */
    public function withMethod($method)
    {
        $clone = clone $this;
        $clone -> setMethod($method);

        return $clone;
    }

    /**
     * Retrieves the URI instance.
     *
     * This method MUST return a UriInterface instance.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     * @return UriInterface Returns a UriInterface instance
     *     representing the URI of the request.
     */
    public function getUri()
    {
        return $this -> _Uri;
    }

    public function setUri(UriInterface $uri)
    {
        $this -> _Uri = $uri;
    }

    /**
     * Returns an instance with the provided URI.
     *
     * This method MUST update the Host header of the returned request by
     * default if the URI contains a host component. If the URI does not
     * contain a host component, any pre-existing Host header MUST be carried
     * over to the returned request.
     *
     * You can opt-in to preserving the original state of the Host header by
     * setting `$preserveHost` to `true`. When `$preserveHost` is set to
     * `true`, this method interacts with the Host header in the following ways:
     *
     * - If the Host header is missing or empty, and the new URI contains
     *   a host component, this method MUST update the Host header in the returned
     *   request.
     * - If the Host header is missing or empty, and the new URI does not contain a
     *   host component, this method MUST NOT update the Host header in the returned
     *   request.
     * - If a Host header is present and non-empty, this method MUST NOT update
     *   the Host header in the returned request.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new UriInterface instance.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     * @param UriInterface $uri New request URI to use.
     * @param bool $preserveHost Preserve the original state of the Host header.
     * @return static
     */
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

            default:
                throw new \InvalidMethodException("No valid method provided.");
                break;
        }

        $this -> _fields = $fields;
    }
}
