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

    public function testTokinizerPunct()
    {
        $tokenizer = Tokenizer::parse('! !<a>!!</a>');
        $tokens = [
            ['type' => 'TextPunct', 'text' => '!'],
            ['type' => 'TextSpace', 'text' => ' '],
            ['type' => 'TextPunct', 'text' => '!'],
            ['type' => 'Html', 'text' => '<a>'],
            ['type' => 'TextPunct', 'text' => '!!'],
            ['type' => 'Html', 'text' => '</a>'],
        ];
        $this->assertEquals($tokens, $tokenizer);
    }

    public function testTokinizerSpace()
    {
        $tokenizer = Tokenizer::parse(" <a> \r\n!</a>");
        $tokens = [
            ['type' => 'TextSpace', 'text' => ' '],
            ['type' => 'Html', 'text' => '<a>'],
            ['type' => 'TextSpace', 'text' => " \r\n"],
            ['type' => 'TextPunct', 'text' => '!'],
            ['type' => 'Html', 'text' => '</a>'],
        ];
        $this->assertEquals($tokens, $tokenizer);
    }

    public function testTokinizerAmp()
    {
        $tokenizer = Tokenizer::parse(" <a> \r\n&nbsp;!&nbsp;&amp;</a>&nbsp<hr>&amp<hr>");
        $tokens = [
            ['type' => 'TextSpace', 'text' => ' '],
            ['type' => 'Html', 'text' => '<a>'],
            ['type' => 'TextSpace', 'text' => " \r\n"],
            ['type' => 'TextSpace', 'text' => '&nbsp;'],
            ['type' => 'TextPunct', 'text' => '!'],
            ['type' => 'TextSpace', 'text' => '&nbsp;'],
            ['type' => 'TextPunct', 'text' => '&amp;'],
            ['type' => 'Html', 'text' => '</a>'],
            ['type' => 'TextSpace', 'text' => '&nbsp'],
            ['type' => 'Html', 'text' => '<hr>'],
            ['type' => 'TextPunct', 'text' => '&amp'],
            ['type' => 'Html', 'text' => '<hr>'],
        ];
        $this->assertEquals($tokens, $tokenizer);
    }
}
