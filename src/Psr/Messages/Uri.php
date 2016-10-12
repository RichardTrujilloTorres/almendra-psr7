<?php

namespace Almendra\Http\Psr\Messages;

use Psr\Http\Message\UriInterface;

use Almendra\Http\Helpers\URI as URIHelper;
use Almendra\Http\Server;

/**
 * Represents an URI.
 *
 * @package Almendra\Psr7    
 * @author     Richard Trujillo Torres     <richard.trujillo.torres@gmail.com>
 */
class Uri implements UriInterface
{
    /**
     * @var string
     */
    protected $_uri;

    /**
     * @var string The URI's scheme
     */
    protected $_scheme = 'blob'; // User-Agent

    /**
     * @var string The URI's query
     */
    protected $_query;

    /**
     * @var string The URI's path
     */
    protected $_path;

    /**
     * @var string The URI's fragment
     */
    protected $_fragment;

    /**
     * @var string The URI's host
     */
    protected $_host;

    /**
     * @var string The URI's port
     */
    protected $_port;

    /**
     * @var string The URI's specified user
     */
    protected $_username;

    /**
     * @var string The URI's specified password
     */
    protected $_password;


    /**
     * Constructs a new URI
     *
     * @param string $uri 		The URI
     * @return boolean			true if success
     */
    public function __construct($uri = null)
    {
        if (isset($uri) && !URIHelper::isValid($uri)) {
            throw new \InvalidArgumentException("Invalid URI");
        }

        $this -> _uri = $uri;
        $this -> _query = URIHelper::getQueryParams($uri);
        $this -> setPath();
        $this -> setFragment();
        $this -> setUser($this -> getUsername());
    }

    /**
     * Return the URI's scheme.
     *
     * @return string
     */
    public function getScheme()
    {
        if (!isset($this -> _scheme) || $this -> _scheme === null) {
            return '';
        }

        return $this -> _scheme;
    }

    /**
     * Returns the user authority in the "[user-info@]host[:port]" format.
     *
     * @param type name 		description
     * @return type 				description
     */
    public function getAuthority()
    {
        $userInfo = $this -> getUserInfo();
        $port = $this -> getPort();

        return ($userInfo ? $userInfo . '@' : '') .
            $this -> getHost() .
            (null !== $port ? ':' . $port : '');
    }
    
    /**
     * Returns user info in the "username[:password]" format.
     *
     * @return string
     */
    public function getUserInfo()
    {
        $userInfo = $this -> getUsername();
        $password = $this -> getPassword();
        if ('' !== $userInfo && '' !== $password) {
            $userInfo .= ':' . $password;
        }

        return $userInfo;
    }

    /**
     * Retrieves the username (user=) specified in the URI.
     *
     * @return string
     */
    public function getUsername()
    {
        $params = URIHelper::getQueryParams($this -> _uri, false);
        if (array_key_exists('username', $params)) {
            return $params['username'];
        }

        return '';
    }

    /**
     * Retrieves the password specified in the URI.
     *
     * @return string
     */
    public function getPassword()
    {
        $params = URIHelper::getQueryParams($this -> _uri, false);
        if (array_key_exists('password', $params)) {
            return $params['password'];
        }

        return '';
    }

    /**
     * Retrieves the URI's host.
     *
     * @return string
     */
    public function getHost()
    {
        $host = strtolower(Server::getValue('SERVER_NAME'));
        
        return $host;
    }

    /**
     * Retrieves the URI's port.
     *
     * @return string
     */
    public function getPort()
    {
        $port = Server::getValue('SERVER_PORT');

        return $port;
    }

    /**
     * Returns the URI's path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this -> _path;
    }

    /**
     * Sets the URI's path.
     *
     * @return void
     */
    protected function setPath()
    {
        // is this the front end controller?
        $path = URIHelper::percentEncode(Server::getValue('PHP_SELF'));

        $path = str_replace('index.php/', '', $path); // rooted
        $path = str_replace('index.php', '', $path); // empty

        $this -> _path = $path;
    }

    /**
     * Retrieve the query string of the URI.
     *
     * If no query string is present, this method MUST return an empty string.
     *
     * The leading "?" character is not part of the query and MUST NOT be
     * added.
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.4.
     *
     * As an example, if a value in a key/value pair of the query string should
     * include an ampersand ("&") not intended as a delimiter between values,
     * that value MUST be passed in encoded form (e.g., "%26") to the instance.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.4
     * @return string The URI query string.
     */
    public function getQuery()
    {
        if (isset($this -> _query) && null !== $this -> _query) {
            return $this -> _query;
        }

        $query = Server::getValue('QUERY_STRING');

        // if not defined, attempt to resolve it
        if ('' === $query) {
            if (strstr($this -> _uri, '?')) {
                $query = substr($this -> _uri, strpos($this -> _uri, '?') + 1);
            }
        }

        return URIHelper::percentEncode($query);
    }

    /**
     * Retrieves the URI's fragment.
     *
     * @return string
     */
    public function getFragment()
    {
        return $this -> _fragment;
    }

    protected function setFragment($fragment = null)
    {
        $this -> _fragment = (isset($fragment) && null !== $fragment) ?
            $this -> _fragment = $fragment :
            $this -> _fragment = URIHelper::getQueryFragment($this -> _uri);
    }

    /**
     * Return an instance with the specified scheme.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified scheme.
     *
     * Implementations MUST support the schemes "http" and "https" case
     * insensitively, and MAY accommodate other schemes if required.
     *
     * An empty scheme is equivalent to removing the scheme.
     *
     * @param string $scheme The scheme to use with the new instance.
     * @return static A new instance with the specified scheme.
     * @throws \InvalidArgumentException for invalid or unsupported schemes.
     */
    public function withScheme($scheme)
    {
        $clone = clone $this;
        $clone -> setScheme($scheme);

        return $clone;
    }

    protected function setScheme($scheme)
    {
        $this -> _scheme = $scheme;
    }

    /**
     * Return an instance with the specified user information.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified user information.
     *
     * Password is optional, but the user information MUST include the
     * user; an empty string for the user is equivalent to removing user
     * information.
     *
     * @param string $user The user name to use for authority.
     * @param null|string $password The password associated with $user.
     * @return static A new instance with the specified user information.
     */
    public function withUserInfo($user, $password = null)
    {
        $clone = clone $this;
        $clone -> setUser($user);
        $clone -> setPassword($password);

        return $clone;
    }

    protected function setUser($user)
    {
        $this -> _username = $user;
    }

    protected function setPassword($password)
    {
        $this -> _password = $password;
    }

    /**
     * Return an instance with the specified host.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified host.
     *
     * An empty host value is equivalent to removing the host.
     *
     * @param string $host The hostname to use with the new instance.
     * @return static A new instance with the specified host.
     * @throws \InvalidArgumentException for invalid hostnames.
     */
    public function withHost($host)
    {
        $clone = clone $this;
        $clone -> _host = $host;

        return $clone;
    }

    /**
     * Return an instance with the specified port.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified port.
     *
     * Implementations MUST raise an exception for ports outside the
     * established TCP and UDP port ranges.
     *
     * A null value provided for the port is equivalent to removing the port
     * information.
     *
     * @param null|int $port The port to use with the new instance; a null value
     *     removes the port information.
     * @return static A new instance with the specified port.
     * @throws \InvalidArgumentException for invalid ports.
     */
    public function withPort($port)
    {
        $clone = clone $this;
        if (!URIHelper::isPortValid($port)) {
            throw new \InvalidArgumentException("Invalid port. The port must be within the TCP and UDP port ranges.");
        }

        $clone -> setPort($port);

        return $clone;
    }

    protected function setPort($port) {
        $this -> _port = $port;
    }

    /**
     * Return an instance with the specified path.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified path.
     *
     * The path can either be empty or absolute (starting with a slash) or
     * rootless (not starting with a slash). Implementations MUST support all
     * three syntaxes.
     *
     * If the path is intended to be domain-relative rather than path relative then
     * it must begin with a slash ("/"). Paths not starting with a slash ("/")
     * are assumed to be relative to some base path known to the application or
     * consumer.
     *
     * Users can provide both encoded and decoded path characters.
     * Implementations ensure the correct encoding as outlined in getPath().
     *
     * @param string $path The path to use with the new instance.
     * @return static A new instance with the specified path.
     * @throws \InvalidArgumentException for invalid paths.
     */
    public function withPath($path)
    {
        $clone = clone $this;
        if (!URIHelper::isPathValid($path)) {
            throw new \InvalidArgumentException("Invalid path.");
        }

        $clone -> setPath($path);

        return $clone;
    }

    public function withQuery($query)
    {
        if (!URIHelper::isQueryValid($query)) {
            throw new \InvalidArgumentException("Invalid query.");
        }

        $clone = clone $this;
        $clone -> setQuery($query);

        return $clone;
    }

    public function setQuery($query)
    {
        $this -> _query = $query;
    }

    public function withFragment($fragment)
    {
        $clone = clone $this;
        $clone -> setFragment($fragment);

        return $clone;
    }

    public function __toString()
    {
        if (is_string($this -> _uri)) {
            return $this -> _uri;
        }

        return '';
    }
}
