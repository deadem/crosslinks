<?php

namespace Test;

use DJEM\Crosslinks\Crosslinks;

class EmptyTest extends \PHPUnit_Framework_TestCase
{
    public function testEmpty()
    {
        $str = 'a b c d';
        $crosslinks = Crosslinks::parse($str, []);
        $this->assertEquals($str, $crosslinks);

        $crosslinks = Crosslinks::parse($str, null);
        $this->assertEquals($str, $crosslinks);

        $crosslinks = Crosslinks::parse($str, '');
        $this->assertEquals($str, $crosslinks);
    }

    public function testTokenizerEmpty()
    {
        $tokenizer = \DJEM\Crosslinks\Tokenizer::parse('');
        $this->assertTrue(true);
    }
}
