<?php

namespace Almendra\Http\Psr\Messages;

use Psr\Http\Message\StreamInterface;
use Exception;


/**
 * Represent a message stream
 *
 * @package Almendra\Http
 */
class Stream implements StreamInterface
{

    /**
     * @var stream body / the resource
     */
    protected $body;

    /**
     * @var The stream uri, if any
     */
    protected $uri;

    /**
     * @var Is the stream seekable?
     */
    protected $seekable;

    /**
     * @var Is the stream readable?
     */
    protected $readable;

    /**
     * @var Is the stream writable?
     */
    protected $writable;

    /**
     * @var The stream metadata, if any
     */
    protected $metaData = [];

    /**
     * @var The overriding options (size, uri, etc.)
     */
    protected $options = [];

    /**
     * @var Default output format
     */
    protected $defaultFormat; // 'JSON'

    /**
     * @var The accepted overriding options
     */
    protected $overridingOptions = [
        'size',
        'uri',
        ];

    /** @var array Hash of readable and writable stream types */
    private static $readWriteHash = [
        'read' => [
            'r' => true, 'w+' => true, 'r+' => true, 'x+' => true, 'c+' => true,
            'rb' => true, 'w+b' => true, 'r+b' => true, 'x+b' => true,
            'c+b' => true, 'rt' => true, 'w+t' => true, 'r+t' => true,
            'x+t' => true, 'c+t' => true, 'a+' => true
        ],
        'write' => [
            'w' => true, 'w+' => true, 'rw' => true, 'r+' => true, 'x+' => true,
            'c+' => true, 'wb' => true, 'w+b' => true, 'r+b' => true,
            'x+b' => true, 'c+b' => true, 'w+t' => true, 'r+t' => true,
            'x+t' => true, 'c+t' => true, 'a' => true, 'a+' => true
        ]
    ];

    /**
     * Sets up the resource.
     *
     * @param resource $stream
     * @param array $options
     * @return void
     */
    public function __construct($stream, $metaData = [], $options = [])
    {
        if (!is_resource($stream)) {
            throw new \InvalidArgumentException("Stream must be a resource.");
        }

        $this -> setBody($stream);
        $this -> setMetadata($stream, $metaData);
        $this -> setOptions($options);
        $this -> setOperations();
    }

    /**
     * Sets the stream metadata.
     *
     * @param resource $stream         The stream
     * @param array $userData         The overriding user metadata
     * @return void
     */
    protected function setMetadata($stream, array $userData = [])
    {
        $meta = stream_get_meta_data($stream);
        $this -> metaData = array_merge($meta, $userData);
    }

    /**
     * Sets the options such as the size, effectively overriding those from the stream.
     *
     * @param array $options         The overriding options
     * @return void
     */
    protected function setOptions(array $options)
    {
        foreach ($this -> overridingOptions as $name) {
            if (isset($options[$name])) {
                $this -> options[$name] = $options[$name];
            }
        }
    }

    /**
     * Reads all data from the stream into a string, from the beginning to end.
     *
     * This method MUST attempt to seek to the beginning of the stream before
     * reading data and read the stream until the end is reached.
     *
     * Warning: This could attempt to load a large amount of data into memory.
     *
     * This method MUST NOT raise an exception in order to conform with PHP's
     * string casting operations.
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
     * @return string
     */
    public function __toString()
    {
        // protect against exception
        try {
            $this -> seek(0);

            $result = (string) stream_get_contents($this -> body);
            if ($this -> isJsonable() && ($this -> defaultFormat === 'JSON')) {
                $result = json_encode($result, JSON_PRETTY_PRINT);
            }
        } catch (Exception $e) {
            $result = '';
        }

        return $result;
    }

    /**
     * Closes the stream and any underlying resources.
     *
     * @return void
     */
    public function close()
    {
        if (!isset($this -> body)) {
            return;
        }

        fclose($this -> body);
        $this -> detach();
    }

    /**
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @return resource|null Underlying PHP stream, if any
     */
    public function detach()
    {
        if (!isset($this -> body)) {
            return null;
        }

        $this -> options = [];
        $this -> metaData = [];
        $this -> unsetOperations();

        $result = $this -> body;
        unset($this -> body);

        return $result;
    }

    /**
     * Get the size of the stream if known.
     *
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    public function getSize()
    {
        if (!isset($this -> options['size'])) {
            try {
                $fstat = fstat($this -> body);
                if (!isset($fstat['size'])) {
                    return null;
                }

                $this -> options['size'] = $fstat['size'];
            } catch (Exception $e) {
                return null; // fails to determine the size
            }
        }

        return $this -> options['size'];
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int Position of the file pointer
     * @throws \RuntimeException on error.
     */
    public function tell()
    {
        if (!$result = ftell($this -> body)) {
            throw new \RuntimeException("Cannot determine the file pointer position.");
        }

        return $result;
    }

    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @return bool
     */
    public function eof()
    {
        // should use feof
        return (!$this->body || feof($this->body));
    }

    /**
     * Returns whether or not the stream is seekable.
     *
     * @return bool
     */
    public function isSeekable()
    {
        return $this -> seekable;
    }

    /**
     * Seek to a position in the stream.
     *
     * @link http://www.php.net/manual/en/function.fseek.php
     * @param int $offset Stream offset
     * @param int $whence Specifies how the cursor position will be calculated
     *     based on the seek offset. Valid values are identical to the built-in
     *     PHP $whence values for `fseek()`.  SEEK_SET: Set position equal to
     *     offset bytes SEEK_CUR: Set position to current location plus offset
     *     SEEK_END: Set position to end-of-stream plus offset.
     * @throws \RuntimeException on failure.
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        if (!$this -> isSeekable()) {
            throw new \RuntimeException("The stream is not seekable.");
        }

        if (fseek($this -> body, $offset, $whence) === -1) {
            throw new \RuntimeException("Could not seek stream position.");
        }
    }

    /**
     * Seek to the beginning of the stream.
     *
     * If the stream is not seekable, this method will raise an exception;
     * otherwise, it will perform a seek(0).
     *
     * @see seek()
     * @link http://www.php.net/manual/en/function.fseek.php
     * @throws \RuntimeException on failure.
     */
    public function rewind()
    {
        $this -> seek(0);
    }

    /**
     * Returns whether or not the stream is writable.
     *
     * @return bool
     */
    public function isWritable()
    {
        return $this -> writable;
    }

    /**
     * Write data to the stream.
     *
     * @param string $string The string that is to be written.
     * @return int Returns the number of bytes written to the stream.
     * @throws \RuntimeException on failure.
     */
    public function write($string)
    {
        if (!$this -> isWritable()) {
            throw new \RuntimeException("Stream is not writable.");
        }

        $this -> options['size'] = null;
        $result = fwrite($this -> body, $string);
        if (false === $result) {
            throw new \RuntimeException("Error while writing to stream.");
        }

        return $result;
    }

    /**
     * Returns whether or not the stream is readable.
     *
     * @return bool
     */
    public function isReadable()
    {
        return $this -> readable;
    }

    /**
     * Read data from the stream.
     *
     * @param int $length Read up to $length bytes from the object and return
     *     them. Fewer than $length bytes may be returned if underlying stream
     *     call returns fewer bytes.
     * @return string Returns the data read from the stream, or an empty string
     *     if no bytes are available.
     * @throws \RuntimeException if an error occurs.
     */
    public function read($length)
    {
        if (!$this -> isReadable()) {
            throw new \RuntimeException("The stream is not readable.");
        }

        return fread($this -> body, $length);
    }

    /**
     * Returns the remaining contents in a string
     *
     * @return string
     * @throws \RuntimeException if unable to read or an error occurs while
     *     reading.
     */
    public function getContents()
    {
        if (!$result = fread($this -> body, $this -> getSize())) {
            throw new \RuntimeException("Unable to read or error while reading the stream.");
        }

        return $result;
    }

    /**
     * Get stream metadata as an associative array or retrieve a specific key.
     *
     * The keys returned are identical to the keys returned from PHP's
     * stream_get_meta_data() function.
     *
     * @link http://php.net/manual/en/function.stream-get-meta-data.php
     * @param string $key Specific metadata to retrieve.
     * @return array|mixed|null Returns an associative array if no key is
     *     provided. Returns a specific key value if a key is provided and the
     *     value is found, or null if the key is not found.
     */
    public function getMetadata($key = null)
    {
        if (!$key) {
            return [];
        }

        if (isset($this -> metaData)) {
            $meta = $this -> metaData;
        } else {
            $meta = stream_get_meta_data($this -> getBody());
        }

        return isset($meta[$key]) ? $meta[$key] : null;
    }

    /**
     * Gets the value of body.
     *
     * @return mixed
     */
    public function getBody()
    {
        if (!isset($this -> body)) {
            return null;
        }

        return $this->body;
    }

    /**
     * Sets the value of body.
     *
     * @param mixed $body the body
     *
     * @return self
     */
    protected function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Is the stream JSONable?
     *
     * @return boolean
     */
    protected function isJsonable()
    {
        $contents = $this -> getBody();
        try {
            $contents = json_encode($contents, JSON_PRETTY_PRINT);
            if ($contents === null ||
                $contents === '') {
                return false;
            }
        } catch (Exception $e) { // Invalid operation
            return false;
        }
        
        return true;
    }

    protected function getOption($name)
    {
        if (!isset($this -> options[$name])) {
            try {
                $fstat = fstat($this -> body);
                if (!isset($fstat[$name])) {
                    return null;
                }

                $this -> options[$name] = $fstat[$name];
            } catch (Exception $e) {
                return null; // fails to determine the size
            }
        }

        return $this -> options[$name];
    }

    protected function setOperations()
    {
        $this -> seekable = $this -> getMetadata('seekable');
        $this -> readable = isset(self::$readWriteHash['read'][$this -> getMetadata('mode')]);
        $this -> writable = isset(self::$readWriteHash['write'][$this -> getMetadata('mode')]);
    }

    protected function unsetOperations()
    {
        $this -> writable = false;
        $this -> readable = false;
        $this -> seekable = false;
    }

    /**
     * Free resources
     *
     * @return void
     */
    public function __destruct()
    {
        $this -> close();
    }
}
