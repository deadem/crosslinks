<?php

namespace Test;

use DJEM\Crosslinks\Crosslinks;

class EmptyTest extends \PHPUnit_Framework_TestCase
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
        $tokenizer = \DJEM\Crosslinks\Tokenizer::parse('');
        $this->assertTrue(true);
    }
}
