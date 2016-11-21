<?php

namespace Test;

use DJEM\Crosslinks\Tokenizer;

class TokenizerTest extends \PHPUNIT_Framework_Testcase
{
    public function testTokinizer()
    {
        $tokenizer = Tokenizer::parse('text<a href="link">link-text</a>more text');
        $this->assertTrue(true);
    }
}
