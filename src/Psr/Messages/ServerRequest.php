<?php

namespace Almendra\Http\Psr\Messages;

use Psr\Http\Message\ServerRequestInterface as RequestInterface;
use Almendra\Http\Psr\Messages\Response;

use Almendra\Http\Helpers\URI as URIHelper;
use Almendra\Http\Server;

class ServerRequest extends Response implements RequestInterface
{
    protected $_cookies = [];
    protected $_serverParams = [];

    // protocol ver
    // headers
    // body


    public function __construct($cookies = null, $serverParams = null) {
        if (isset($cookies) && null !== $cookies) {
            $this -> setCookies($cookies);
        } else {
            $this -> setCookies($this -> getCookieParams());
        }
        
    }
    
    /**
     * Retrieve server parameters.
     *
     * Retrieves data related to the incoming request environment,
     * typically derived from PHP's $_SERVER superglobal. The data IS NOT
     * REQUIRED to originate from $_SERVER.
     *
     * @return array
     */
    public function getServerParams() {
        if (isset($_SERVER) && null !== $_SERVER) {
            $this -> _serverParams = $_SERVER;
        }

        return $this -> _serverParams; // attempt to retrieve them
    }


    /**
     * Retrieve cookies.
     *
     * Retrieves cookies sent by the client to the server.
     *
     * The data MUST be compatible with the structure of the $_COOKIE
     * superglobal.
     *
     * @return array
     */
    public function getCookieParams()
    {
        return $this -> _cookies;
    }

    public function withCookieParams(array $cookies)
    {
        $clone = clone $this;
        $clone -> setCookies($cookies);

        return $clone;
    }

    public function setCookies(array $cookies)
    {
        foreach ($cookies as $name => $value) {
            $this -> setCookie($name, $value);
        }
    }

    public function setCookie($name, $value)
    {
        $this -> _cookies[$name] = $value;
    }

    /**
     * Retrieve query string arguments.
     *
     * Retrieves the deserialized query string arguments, if any.
     *
     * Note: the query params might not be in sync with the URI or server
     * params. If you need to ensure you are only getting the original
     * values, you may need to parse the query string from `getUri()->getQuery()`
     * or from the `QUERY_STRING` server param.
     *
     * @return array
     */
    public function getQueryParams()
    {
        return URIHelper::getQueryParams($this -> getUri(), false);
    }
}
