<?php

namespace Test;

use DJEM\Crosslinks\Crosslinks;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    public function testSingle()
    {
        $crosslinks = Crosslinks::parse('a b c d', ['a' => '/a-link']);
        $this->assertEquals('<a href="/a-link">a</a> b c d', $crosslinks);
    }
}
