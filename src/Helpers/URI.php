<?php

namespace Almendra\Http\Helpers;

// use Almendra\Http\Psr\Messages\Uri as PsrUri;
use Psr\Http\Message\UriInterface as PsrUri;

class URI
{
    /**
     * Retrieve the parameters from the URI
     *
     * @param type name 		description
     * @return type 				description
     */
    public static function getQueryParams($uri, $serialized = true)
    {
        $params = '';

        // do parameters exist?
        if (strstr($uri, '?')) {
            $params = substr($uri, strpos($uri, '?') + 1);
        }

        // check the parameters
        if (!self::isQueryParamsValid($params)) {
            throw new \InvalidArgumentException("Invalid query parameters");
        }

        if (!$serialized) {
            $params = static::deserializeQueryParams($params);
        }

        return $params;
    }

    /**
     * Returns the query params as an associative array
     *
     * @param string $params 		The query parameters
     * @return array
     */
    public static function deserializeQueryParams($params)
    {
        $fields = [];

        $params = explode('&', $params);
        foreach ($params as $param) {
            $parts = explode('=', $param);

            // eliminate not defined parameters
            if (count($parts) != 2) {
                continue;
            } elseif (null === $parts[0] || '' === $parts[0]) {
                continue;
            }

            $fields[$parts[0]] = $parts[1];
        }

        return $fields;
    }

    /**
     * Serializes the query params
     *
     * @param array $params 		The query params
     * @return string
     */
    public static function serializeQueryParams(array $queryParams)
    {
        $params = '';
        foreach ($queryParams as $key => $value) {
            $params .= $key . '=' . $value . '&';
        }

        return substr($params, 0, -1);
    }

    /**
     * Check query parameters validity
     *
     * @param string $params 		The query parameters
     * @return boolean 				true if valid
     */
    public static function isQueryParamsValid($params)
    {
        if (is_array($params)) {
            throw new \InvalidArgumentException("Query parameters must be an string");
        }

        $params = self::deserializeQueryParams($params);
        
        return true;
    }

    /**
     * Percentage encode a query.
     *
     * @param string $query 		The query to be encoded.
     * @return string
     */
    public static function percentEncode($query)
    {
        return htmlentities(strip_tags($query), ENT_QUOTES);
    }

    public static function isQueryValid($query)
    {
        if (!is_string($query) && !method_exists($query, '__toString')) {
            return false;
        }

        return true;
    }

    public static function isValid($uri)
    {
        if (!($uri instanceof PsrUri) && !is_string($uri)) {
            return false;
        }

        return true;
    }

    /**
     * Retrieve the fragment from the URI
     *
     * @param string $uri 		The URI
     * @return string
     */
    public static function getQueryFragment($uri)
    {
        return null;
    }

    /**
     * Validates a port within TCP and UDP ranges.
     *
     * @param string $port         The port
     * @return boolean
     */
    public static function isPortValid($port)
    {
        return true;
    }

    /**
     * Validates a path.
     *
     * @param string $path         The path
     * @return boolean
     */
    public static function isPathValid($path)
    {
        return true;
    }

    // @todo $validator = new Validator::make($rules);
    public function validator()
    {
        return [
            'name' => [
                'min' => 255,
                ],

            'value' => [
                ],
            ];
    }
}
