<?php

namespace Almendra\Http\Interfaces;


interface ServerInterface {
	/**
	 * Retrieves a value define in the superglobal $_SERVER.
	 *
	 * @param string $value 		The key's name.
	 * @return string|mixed 				
	 */
	public static function getValue($value, $default = '');

	public function get();

	public function post();

	public function files();

	public function all();

	//
}