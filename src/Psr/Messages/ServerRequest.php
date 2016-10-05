<?php

namespace Almendra\Http\Psr\Messages;

use Psr\Http\Message\ServerRequestInterface as RequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\Environment;

class ServerRequest extends Message implements RequestInterface
{
    protected $_cookies = [];
    protected $_serverParams = [];

    // protocol ver
    // headers
    // body

    public function getServerParams()
    {
        return $this -> _serverParams;
    }

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
     * Returns the deserialized query parameters
     *
     * @return array 				The query parameters
     */
    public function getQueryParams()
    {
        return true;
    }
}
