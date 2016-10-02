<?php

namespace Almendra\Http\Psr\Messages;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;


class Message implements MessageInterface {
	protected $_protocolVersion = '1.1';

	// @todo use a header collection instead
	protected $_headers = []; 

	protected $_body;

	/**
	 * @var array The headers which are accepted. 
	 */
	protected $_validHeaders = [
		'Content-Type',
		'Connection',
		'Accept',
		'Accept-Encoding',
		'Accept-Language',
		'Cache-Control',
		'Cookie',
		'Host',
		'User-Agent',
		'Remote Address',
		// ...
		];

	/**
	 * @var array Valid/Supported HTTP Protocol versions .
	 */
	protected $_validVersions = [
		'1.0',
		'1.1',
		'2.0',
		];


	/**
	 * Returns the message's protocol version.
	 *
	 * @return string 				
	 */
	public function getProtocolVersion() {
		return $this -> _protocolVersion;
	}

	/**
	 * Returns an instance of the message with the specified protocol version.
	 *
	 * @param string $version 		The protocol version.
	 * @return \Almendra\Http\Psr\Messages\Message 	
	 * @throws InvalidArgumentException			
	 */
	public function withProtocolVersion($version) {
		if (!in_array($version, $this -> _validVersions)) {
			throw new \InvalidArgumentException("Invalid or unsupported HTTP version.");		
		}

		$this -> _protocolVersion = $version;

		return $this;
	}

	/**
	 * Returns the headers.
	 *
	 * @return array 				An associative array of string values.
	 */
	public function getHeaders() {
		return $this -> _headers;
	}

	/**
	 * Check if a header exists.
	 *
	 * @param string $name 		The name of the header.
	 * @return boolean 				
	 */
	public function hasHeader($name) {
		$name = strtolower($name);
		if (isset($this -> _headers[$name])) {
			return true;
		}

		return false;
	}

	/**
	 * Returns a header.
	 *
	 * @param string $name 		The header's name. 
	 * @return string 				
	 */
	public function getHeader($name) {
		$name = strtolower($name);
		if ($this -> hasHeader($name)) {

			return is_array($this -> _headers[$name]) ? 
				$this -> _headers[$name] :
				[$this -> _headers[$name]];
		} 

		return [];
	}

	/**
	 * Returns a comma-separated header.
	 *
	 * @param string $name 		The header's name.
	 * @return string 				
	 */
	public function getHeaderLine($name) {
		if (!$this -> hasHeader($name)) {
			return '';
		}

		if (!is_array($header = $this -> getHeader($name))) {
			return $header;
		}

		$header = implode(', ', $this -> getHeader($name));

		return $header;
	}

	/**
	 * Returns a message instance with the specified header
	 * Replaces the header if it exists.
	 *
	 * @param string $name 		The header's name
	 * @param string $value 	The header's value 
	 * @return \Almendra\Http\Psr\Messages\Message 				
	 */
	public function withHeader($name, $value) {
		if (!$this -> isHeaderValid($name)) {
			throw new \InvalidArgumentException("Invalid header name provided.");	
		}

		$clone = clone $this;
		$clone -> setHeader($name, $value); // replace if exists

		return $clone;
	}

	/**
	 * Returns a message instance with the an added header.
	 * Does not replace the if it exists already.
	 *
	 * @param string $name 		The header's name
	 * @param string $value 	The header's value 
	 * @return \Almendra\Http\Psr\Messages\Message 				
	 */
	public function withAddedHeader($name, $value) {
		if (!$this -> isHeaderValid($name)) {
			throw new \InvalidArgumentException("Invalid header name provided.");	
		}

		// append only
		$clone = clone $this;
		if (!$clone -> hasHeader($name)) {
			$clone -> setHeader($name, $value);
		} else {
			$clone -> addHeader($name, $value);
		}
		
		return $clone;
	}

	/**
	 * Checks whather a header is valid.
	 *
	 * @param string $name 		The header's name
	 * @return boolean			true if supported
	 */
	public function isHeaderValid($name) {
		$name = strtolower($name);
		if (!is_string($name)) {
			return false;
			
		}

		return true;
	}

	/**
	 * Sets a header, by name.
	 *
	 * @param string $name 		The header's name
	 * @param string $name 		The header's value
	 * @return void 
	 * @throws \InvalidArgumentException				
	 */
	public function setHeader($name, $value) {
		if (!$this -> isHeaderValid($name)) {
			throw new \InvalidArgumentException("Invalid header name provided.");
		}

		$name = strtolower($name);
		$this -> _headers[$name] = $value;
	}

	public function addHeader($name, $value) {
	    if (!is_array($value)) {
	        $value = [$value];
	    }

	    $header = $this -> getHeader($name);
	    $this -> setHeader($name, array_merge($header,$value));
	}

	/**
	 * Returns an instance of the message without the specified header.
	 *
	 * @param string $name 		The header's name
	 * @return \Almendra\Http\Psr\Messages\Message 	
	 */
	public function withoutHeader($name) {
		$clone = clone $this;
		$clone -> unsetHeader($name);

		return $clone;
	}

	/**
	 * Unsets a header.
	 *
	 * @param string $name 		The header's name.
	 * @return void 				
	 */
	public function unsetHeader($name) {
		unset($this -> _headers[$name]);
	}

	/**
	 * Returns the message's body.
	 *
	 * @return Psr\Http\Message\StreamInterface 				
	 */
	public function getBody() {
		if (!isset($this -> _body) || null === $this -> _body) {
			$this -> _body = new \Almendra\Http\Psr\Messages\Stream;
		}
		
		return $this -> _body;
	}

	/**
	 * Returns an instance of the message with the specified body.
	 *
	 * @param \Psr\Http\Message\StreamInterface $body 		The body to be added
	 * @return \Almendra\Http\Psr\Messages\Message 
	 * @throws \InvalidArgumentException	
	 */
	public function withBody(StreamInterface $body) {
		$clone = clone $this;
		$clone -> _body = $body;

		return $clone;
	}

	
}