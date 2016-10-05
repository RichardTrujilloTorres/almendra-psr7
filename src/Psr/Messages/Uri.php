<?php

namespace Almendra\Http\Psr\Messages;

use Psr\Http\Message\UriInterface;

use Almendra\Http\Helpers\URI as URIHelper;
use Almendra\Http\Server;

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
    protected $_user;

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
        // scheme
        // $this -> setScheme();
        // ...

        return true;
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
        $userInfo = '';
        return $this -> getUsername() . ':' . $this -> getPassword();
    }

    /**
     * Retrieves the username (user=) specified in the URI.
     *
     * @return string
     */
    public function getUsername()
    {
        $params = URIHelper::getQueryParams($this -> _uri, false);
        if (array_key_exists('user', $params)) {
            return $params['user'];
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
     * Retrieves the query string from the URI.
     *
     * @return string
     */
    public function getQuery()
    {
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

    protected function setFragment()
    {
        $this -> _fragment = URIHelper::getQueryFragment($this -> _uri);
    }

    public function withScheme($scheme)
    {
        $clone = clone $this;
        $clone -> setScheme($scheme);

        return $clone;
    }

    protected function setScheme($scheme)
    {
        // @todo validate scheme
        $this -> _scheme = $scheme;
    }


    public function withUserInfo($user, $password = null)
    {
        $clone = clone $this;
        $clone -> setUser($user);
        $clone -> setPassword($password);

        return $clone;
    }

    protected function setUser($user)
    {
        $this -> _user = $user;
    }

    protected function setPassword($password)
    {
        $this -> _password = $password;
    }

    public function withHost($host)
    {
        $clone = clone $this;
        $clone -> _host = $host;

        return $clone;
    }

    public function withPort($port)
    {
        return true;
    }

    public function withPath($path)
    {
        return true;
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
        return true;
    }

    public function __toString()
    {
        if (is_string($this -> _uri)) {
            return $this -> _uri;
        }

        return '';
    }
}
