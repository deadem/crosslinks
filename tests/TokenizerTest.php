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
        $tokenizer = Tokenizer::parse("&nbsp;&nbsp; <a> \r\n&nbsp;!&nbsp;&amp;</a>&nbsp;<hr>&amp;<hr>");
        $tokens = [
            ['type' => 'TextAmp', 'text' => '&nbsp;'],
            ['type' => 'TextAmp', 'text' => '&nbsp;'],
            ['type' => 'TextSpace', 'text' => ' '],
            ['type' => 'Html', 'text' => '<a>'],
            ['type' => 'TextSpace', 'text' => " \r\n"],
            ['type' => 'TextAmp', 'text' => '&nbsp;'],
            ['type' => 'TextPunct', 'text' => '!'],
            ['type' => 'TextAmp', 'text' => '&nbsp;'],
            ['type' => 'TextAmp', 'text' => '&amp;'],
            ['type' => 'Html', 'text' => '</a>'],
            ['type' => 'TextAmp', 'text' => '&nbsp;'],
            ['type' => 'Html', 'text' => '<hr>'],
            ['type' => 'TextAmp', 'text' => '&amp;'],
            ['type' => 'Html', 'text' => '<hr>'],
        ];
        $this->assertEquals($tokens, $tokenizer);

        $tokenizer = Tokenizer::parse('&amp;!!&amp; text');
        $tokens = [
            ['type' => 'TextAmp', 'text' => '&amp;'],
            ['type' => 'TextPunct', 'text' => '!!'],
            ['type' => 'TextAmp', 'text' => '&amp;'],
            ['type' => 'TextSpace', 'text' => ' '],
            ['type' => 'Text', 'text' => 'text'],
        ];
        $this->assertEquals($tokens, $tokenizer);
    }

    public function testTokinizerComment()
    {
        $tokenizer = Tokenizer::parse('text<!--a href="link" single=\'quote\'>link---?text</a-->more text');

        $tokens = [
            ['type' => 'Text', 'text' => 'text'],
            ['type' => 'Comment', 'text' => '<!--a href="link" single=\'quote\'>link---?text</a-->'],
            ['type' => 'Text', 'text' => 'more'],
            ['type' => 'TextSpace', 'text' => ' '],
            ['type' => 'Text', 'text' => 'text'],
        ];

        $this->assertEquals($tokens, $tokenizer);
    }
}
