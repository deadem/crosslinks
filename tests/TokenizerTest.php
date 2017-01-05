<?php

namespace Test;

use DJEM\Crosslinks\Tokenizer;

class TokenizerTest extends \PHPUnit_Framework_TestCase
{
    public function testTokinizer()
    {
        $str = 'text<a href="link" single=\'quote\'>link---?text</a>more text';
        $tokenizer = Tokenizer::parse($str);

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
        $this->assertEquals($str, Tokenizer::toString($tokenizer));
    }

    public function testTokinizerPunct()
    {
        $str = '! !<a>!!</a>';
        $tokenizer = Tokenizer::parse($str);
        $tokens = [
            (object) ['type' => 'TextPunct', 'text' => '!'],
            (object) ['type' => 'TextSpace', 'text' => ' '],
            (object) ['type' => 'TextPunct', 'text' => '!'],
            (object) ['type' => 'Html', 'text' => '<a>'],
            (object) ['type' => 'TextPunct', 'text' => '!!'],
            (object) ['type' => 'Html', 'text' => '</a>'],
        ];
        $this->assertEquals($tokens, $tokenizer);
        $this->assertEquals($str, Tokenizer::toString($tokenizer));
    }

    public function testTokinizerSpace()
    {
        $str = " <a> \r\n!</a>";
        $tokenizer = Tokenizer::parse($str);
        $tokens = [
            (object) ['type' => 'TextSpace', 'text' => ' '],
            (object) ['type' => 'Html', 'text' => '<a>'],
            (object) ['type' => 'TextSpace', 'text' => " \r\n"],
            (object) ['type' => 'TextPunct', 'text' => '!'],
            (object) ['type' => 'Html', 'text' => '</a>'],
        ];
        $this->assertEquals($tokens, $tokenizer);
        $this->assertEquals($str, Tokenizer::toString($tokenizer));
    }

    public function testTokinizerAmp()
    {
        $str = "&nbsp;&nbsp; <a> \r\n&nbsp;!&nbsp;&amp;</a>&nbsp;<hr>&amp;<hr>";
        $tokenizer = Tokenizer::parse($str);
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
        $this->assertEquals($str, Tokenizer::toString($tokenizer));

        $str = '&amp;!!&amp<hr>; text';
        $tokenizer = Tokenizer::parse($str);
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
        $this->assertEquals($str, Tokenizer::toString($tokenizer));
    }

    public function testTokinizerComment()
    {
        $str = 'text<!--a href="link" single=\'quote\'>link---?text</a-->more text';
        $tokenizer = Tokenizer::parse($str);

        $tokens = [
            (object) ['type' => 'Text', 'text' => 'text'],
            (object) ['type' => 'Comment', 'text' => '<!--a href="link" single=\'quote\'>link---?text</a-->'],
            (object) ['type' => 'Text', 'text' => 'more'],
            (object) ['type' => 'TextSpace', 'text' => ' '],
            (object) ['type' => 'Text', 'text' => 'text'],
        ];

        $this->assertEquals($tokens, $tokenizer);
        $this->assertEquals($str, Tokenizer::toString($tokenizer));
    }

    public function testTokinizerScript()
    {
        $str = 'text<script><a href="link" single=\'quote\'>link---?text</a></script>more text';
        $tokenizer = Tokenizer::parse($str);

        $tokens = [
            (object) ['type' => 'Text', 'text' => 'text'],
            (object) ['type' => 'Script', 'text' => '<script><a href="link" single=\'quote\'>link---?text</a></script>'],
            (object) ['type' => 'Text', 'text' => 'more'],
            (object) ['type' => 'TextSpace', 'text' => ' '],
            (object) ['type' => 'Text', 'text' => 'text'],
        ];

        $this->assertEquals($tokens, $tokenizer);
        $this->assertEquals($str, Tokenizer::toString($tokenizer));
    }

    public function testTokinizerStyle()
    {
        $str = 'text<style><a href="link" single=\'quote\'>link---?text</a></style>more text';
        $tokenizer = Tokenizer::parse($str);

        $tokens = [
            (object) ['type' => 'Text', 'text' => 'text'],
            (object) ['type' => 'Style', 'text' => '<style><a href="link" single=\'quote\'>link---?text</a></style>'],
            (object) ['type' => 'Text', 'text' => 'more'],
            (object) ['type' => 'TextSpace', 'text' => ' '],
            (object) ['type' => 'Text', 'text' => 'text'],
        ];

        $this->assertEquals($tokens, $tokenizer);
        $this->assertEquals($str, Tokenizer::toString($tokenizer));
    }
}
