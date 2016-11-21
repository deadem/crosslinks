<?php

namespace Test;

use DJEM\Crosslinks;

class EmptyTest extends \PHPUNIT_Framework_Testcase
{
    public function testEmpty()
    {
        $crosslinks = Crosslinks::parse('a b c d', ['a' => '/a-link']);
        $this->assertTrue(true);
    }
}
