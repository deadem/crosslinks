<?php

namespace Test;

use DJEM\Crosslinks\Crosslinks;

class EmptyTest extends \PHPUNIT_Framework_Testcase
{
    public function testEmpty()
    {
        $crosslinks = Crosslinks::parse('a b c d', ['a' => '/a-link']);
        $this->assertTrue(true);

        $crosslinks = Crosslinks::parse('a b c d', null);
        $this->assertTrue(true);

        $crosslinks = Crosslinks::parse('a b c d', '');
        $this->assertTrue(true);
    }

    public function testTokenizerEmpty()
    {
        $tokenizer = new \DJEM\Crosslinks\Tokenizer();
        $this->assertTrue(true);
    }
}
