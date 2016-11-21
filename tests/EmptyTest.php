<?php

namespace Test;

use DJEM\Crosslinks;

class EmptyTest extends \PHPUNIT_Framework_Testcase
{
    public function testEmpty()
    {
        $crosslinks = new Crosslinks();
        $this->assertTrue(true);
    }
}
