<?php

namespace Test;

use DJEM\Crosslinks\Tokenizer;

class TokenizerTest extends \PHPUNIT_Framework_Testcase
{
    public function testTokinizer()
    {
        $tokenizer = Tokenizer::parse('text<a href="link" single=\'quote\'>link---?text</a>more text');

        $tokens = [
            (object) ['type' => 'Text', 'text' => 'text'],
            (object) ['type' => 'Html', 'text' => '<a href="link" single=\'quote\'>'],
            (object) ['type' => 'Text', 'text' => 'link'],
            (object) ['type' => 'TextPunct', 'text' => '---?'],
            (object) ['type' => 'Text', 'text' => 'text'],
            (object) ['type' => 'Html', 'text' => '</a>'],
            (object) ['type' => 'Text', 'text' => 'more'],
            (object) ['type' => 'TextSpace', 'text' => ' '],
            (object) ['type' => 'Text', 'text' => 'text'],
        ];

        $this->assertEquals($tokens, $tokenizer);
    }

    public function testTokinizerPunct()
    {
        $tokenizer = Tokenizer::parse('! !<a>!!</a>');
        $tokens = [
            (object) ['type' => 'TextPunct', 'text' => '!'],
            (object) ['type' => 'TextSpace', 'text' => ' '],
            (object) ['type' => 'TextPunct', 'text' => '!'],
            (object) ['type' => 'Html', 'text' => '<a>'],
            (object) ['type' => 'TextPunct', 'text' => '!!'],
            (object) ['type' => 'Html', 'text' => '</a>'],
        ];
        $this->assertEquals($tokens, $tokenizer);
    }

    public function testTokinizerSpace()
    {
        $tokenizer = Tokenizer::parse(" <a> \r\n!</a>");
        $tokens = [
            (object) ['type' => 'TextSpace', 'text' => ' '],
            (object) ['type' => 'Html', 'text' => '<a>'],
            (object) ['type' => 'TextSpace', 'text' => " \r\n"],
            (object) ['type' => 'TextPunct', 'text' => '!'],
            (object) ['type' => 'Html', 'text' => '</a>'],
        ];
        $this->assertEquals($tokens, $tokenizer);
    }

    public function testTokinizerAmp()
    {
        $tokenizer = Tokenizer::parse("&nbsp;&nbsp; <a> \r\n&nbsp;!&nbsp;&amp;</a>&nbsp;<hr>&amp;<hr>");
        $tokens = [
            (object) ['type' => 'TextAmp', 'text' => '&nbsp;'],
            (object) ['type' => 'TextAmp', 'text' => '&nbsp;'],
            (object) ['type' => 'TextSpace', 'text' => ' '],
            (object) ['type' => 'Html', 'text' => '<a>'],
            (object) ['type' => 'TextSpace', 'text' => " \r\n"],
            (object) ['type' => 'TextAmp', 'text' => '&nbsp;'],
            (object) ['type' => 'TextPunct', 'text' => '!'],
            (object) ['type' => 'TextAmp', 'text' => '&nbsp;'],
            (object) ['type' => 'TextAmp', 'text' => '&amp;'],
            (object) ['type' => 'Html', 'text' => '</a>'],
            (object) ['type' => 'TextAmp', 'text' => '&nbsp;'],
            (object) ['type' => 'Html', 'text' => '<hr>'],
            (object) ['type' => 'TextAmp', 'text' => '&amp;'],
            (object) ['type' => 'Html', 'text' => '<hr>'],
        ];
        $this->assertEquals($tokens, $tokenizer);

        $tokenizer = Tokenizer::parse('&amp;!!&amp<hr>; text');
        $tokens = [
            (object) ['type' => 'TextAmp', 'text' => '&amp;'],
            (object) ['type' => 'TextPunct', 'text' => '!!'],
            (object) ['type' => 'TextAmp', 'text' => '&amp'],
            (object) ['type' => 'Html', 'text' => '<hr>'],
            (object) ['type' => 'TextPunct', 'text' => ';'],
            (object) ['type' => 'TextSpace', 'text' => ' '],
            (object) ['type' => 'Text', 'text' => 'text'],
        ];
        $this->assertEquals($tokens, $tokenizer);
    }

    public function testTokinizerComment()
    {
        $tokenizer = Tokenizer::parse('text<!--a href="link" single=\'quote\'>link---?text</a-->more text');

        $tokens = [
            (object) ['type' => 'Text', 'text' => 'text'],
            (object) ['type' => 'Comment', 'text' => '<!--a href="link" single=\'quote\'>link---?text</a-->'],
            (object) ['type' => 'Text', 'text' => 'more'],
            (object) ['type' => 'TextSpace', 'text' => ' '],
            (object) ['type' => 'Text', 'text' => 'text'],
        ];

        $this->assertEquals($tokens, $tokenizer);
    }

    public function testTokinizerScript()
    {
        $tokenizer = Tokenizer::parse('text<script><a href="link" single=\'quote\'>link---?text</a></script>more text');

        $tokens = [
            (object) ['type' => 'Text', 'text' => 'text'],
            (object) ['type' => 'Script', 'text' => '<script><a href="link" single=\'quote\'>link---?text</a></script>'],
            (object) ['type' => 'Text', 'text' => 'more'],
            (object) ['type' => 'TextSpace', 'text' => ' '],
            (object) ['type' => 'Text', 'text' => 'text'],
        ];

        $this->assertEquals($tokens, $tokenizer);
    }

    public function testTokinizerStyle()
    {
        $tokenizer = Tokenizer::parse('text<style><a href="link" single=\'quote\'>link---?text</a></style>more text');

        $tokens = [
            (object) ['type' => 'Text', 'text' => 'text'],
            (object) ['type' => 'Style', 'text' => '<style><a href="link" single=\'quote\'>link---?text</a></style>'],
            (object) ['type' => 'Text', 'text' => 'more'],
            (object) ['type' => 'TextSpace', 'text' => ' '],
            (object) ['type' => 'Text', 'text' => 'text'],
        ];

        $this->assertEquals($tokens, $tokenizer);
    }
}
