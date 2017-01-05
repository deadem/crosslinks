<?php

namespace Test;

use DJEM\Crosslinks\Crosslinks;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    public function testSingle()
    {
        $crosslinks = Crosslinks::parse('a b c d', ['a' => '/a-link']);
        $this->assertEquals('<a href="/a-link">a</a> b c d', $crosslinks);

        $crosslinks = Crosslinks::parse('a b c d', ['c' => '/a-link']);
        $this->assertEquals('a b <a href="/a-link">c</a> d', $crosslinks);
    }

    public function testDouble()
    {
        $crosslinks = Crosslinks::parse('a b c d', ['b c' => '/a-link']);
        $this->assertEquals('a <a href="/a-link">b c</a> d', $crosslinks);

        $crosslinks = Crosslinks::parse('a b c d', ['c b' => '/a-link']);
        $this->assertEquals('a <a href="/a-link">b c</a> d', $crosslinks);

        $crosslinks = Crosslinks::parse('a b c d e', ['c d b' => '/a-link']);
        $this->assertEquals('a <a href="/a-link">b c d</a> e', $crosslinks);

        $crosslinks = Crosslinks::parse('a b c d', ['c d b' => '/a-link']);
        $this->assertEquals('a <a href="/a-link">b c d</a>', $crosslinks);
    }

    public function testHtml()
    {
        $crosslinks = Crosslinks::parse('a <b>b c</b> d', ['b c' => '/a-link']);
        $this->assertEquals('a <b><a href="/a-link">b c</a></b> d', $crosslinks);

        $crosslinks = Crosslinks::parse('a <b>b</b> <i>c</i> d', ['b c' => '/a-link']);
        $this->assertEquals('a <b><a href="/a-link">b</a></b> <i><a href="/a-link">c</a></i> d', $crosslinks);
    }

    public function testStemming()
    {
        function stemming_test_one($word)
        {
            return substr($word, 0, 10);
        }

        function stemming_test_two($word)
        {
            return substr($word, 0, 4);
        }

        $crosslinks = Crosslinks::parse('a <b>testing functions</b> d', ['test function' => '/a-link'], [
            'Test\\stemming_test_one',
            'Test\\stemming_test_two',
        ]);
        $this->assertEquals('a <b><a href="/a-link">testing functions</a></b> d', $crosslinks);
    }
}