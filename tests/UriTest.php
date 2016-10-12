<?php

namespace Test;

use Almendra\Http\Psr\Messages\Uri;
use Test\DummyObject;

class UriTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     *
     * Test that it gets the scheme properly
     */
    public function it_gets_the_proper_scheme()
    {
        $uri = new Uri;

        $this -> assertEquals($uri -> withScheme('this is a sample scheme') -> getScheme(),
            'this is a sample scheme');
    }

    /**
     * @test
     *
     * Test that it returns the Uri as a string
     */
    public function it_returns_the_uri_as_a_string()
    {
        $uriStr = 'test uri';
        $uri = new Uri($uriStr);

        $this -> assertEquals($uri, $uriStr);
    }

    /**
     * @test
     *
     * Test that it returns a new instance of the Uri with the specified fragment
     */
    public function it_returns_the_specified_uri_fragment()
    {
        $fragment = 'some test fragment here';
        $uri = new Uri();
        $newUri = $uri -> withFragment($fragment);

        // fails
        $this -> assertFalse($uri -> getFragment() === $fragment);

        // passes
        $this -> assertTrue($newUri -> getFragment() === $fragment);

        // passes --diff instance
        $this -> assertEquals($uri === $newUri, false);
    }

    /**
     * @test
     *
     * Test that it returns the specified query string
     */
    public function it_returns_the_specified_query_string()
    {
        $query = 'some test query string here';
        $uri = new Uri();
        $newUri = $uri -> withQuery($query);

        // fails
        $this -> assertFalse($uri -> getQuery() === $query);

        // passes
        $this -> assertTrue($newUri -> getQuery() === $query);

        // passes --diff instance
        $this -> assertEquals($uri === $newUri, false);
    }

    /**
     * @test
     *
     * Test that it validates the query string
     */
    public function it_validates_the_query_string()
    {
        $query = 2323; // throws \InvalidArgumentException
        $uri = new Uri();

        try {
            $newUri = $uri -> withQuery($query);
        } catch (\InvalidArgumentException $e) {
            $result = true;
        }

        $this -> assertTrue($result);

        // implements __toString() --throws no exception
        $queryObject = new DummyObject($query);
        try {
            $newUri = $uri -> withQuery($query);
        } catch (\InvalidArgumentException $e) {
            $result = false;
        }

        $this -> assertFalse($result);
    }


    // path validity
    // withpath()
    // withHost()
    // withPort()
    //
}
