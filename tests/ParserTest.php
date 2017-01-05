<?php

namespace Test;

use DJEM\Crosslinks\Crosslinks;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    public function testSingle()
    {
        $crosslinks = Crosslinks::parse('a b c d', ['a' => '/a-link']);
        $this->assertEquals('<a href="/a-link">a</a> b c d', $crosslinks);

        $crosslinks = Crosslinks::parse('a b c d', ['c' => '/a-link']);
        $this->assertEquals('a b <a href="/a-link">c</a> d', $crosslinks);
    }

    public function testDouble()
    {
        $crosslinks = Crosslinks::parse('a b c d', ['b c' => '/a-link']);
        $this->assertEquals('a <a href="/a-link">b c</a> d', $crosslinks);

        $crosslinks = Crosslinks::parse('a b c d', ['c b' => '/a-link']);
        $this->assertEquals('a <a href="/a-link">b c</a> d', $crosslinks);
    }
}
