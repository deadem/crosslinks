<?php

namespace Test;

use DJEM\Crosslinks\Tokenizer;

class TokenizerTest extends \PHPUNIT_Framework_Testcase
{
    public function testTokinizer()
    {
        $tokenizer = Tokenizer::parse('text<a href="link" single=\'quote\'>link---?text</a>more text');

        $tokens = [
            ['type' => 'Text', 'text' => 'text'],
            ['type' => 'Html', 'text' => '<a href="link" single=\'quote\'>'],
            ['type' => 'Text', 'text' => 'link'],
            ['type' => 'TextPunct', 'text' => '---?'],
            ['type' => 'Text', 'text' => 'text'],
            ['type' => 'Html', 'text' => '</a>'],
            ['type' => 'Text', 'text' => 'more'],
            ['type' => 'TextSpace', 'text' => ' '],
            ['type' => 'Text', 'text' => 'text'],
        ];

        $this->assertEquals($tokens, $tokenizer);
    }
}
