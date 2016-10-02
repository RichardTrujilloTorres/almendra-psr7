<?php

namespace Almendra\Http;

use Almendra\Http\Interfaces\ServerInterface;


class Server implements ServerInterface {
	/**
	 * Retrieves a value define in the superglobal $_SERVER.
	 *
	 * @param string $value 		The key's name.
	 * @return string|mixed 				
	 */
	public static function getValue($value, $default = '') {
        if (array_key_exists($value, $_SERVER)) {
            return $_SERVER[$value];
        }

        return $default;
    }

    public function get() {
    	$this -> _fields = $_GET;
    	return $this;
    }

    public function post() {
    	$this -> _fields = $_POST;
    	return $this;
    }

    public function files() {
    	$this -> _fields = $_FILES;
    	return $this;
    }

    public function all() {
    	return $this -> _fields;	
    }


}