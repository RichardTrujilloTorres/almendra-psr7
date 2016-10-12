<?php

namespace Test;

/**
 * Mimics real object behavior
 *
 * @package default
 * @author     author     <email>
 */
class DummyObject
{
    protected $body;

    public function __construct($body)
    {
        $this -> body = $body;
    }
    
    public function __toString()
    {
        return $this -> body;
    }

    protected function getTestUris()
    {
        return [
            'some bizzare series of Uris',
            'some bizzare series of Uris',
            'adafa89354925054035^&$$*&)($@_',
            ];
    }
}
