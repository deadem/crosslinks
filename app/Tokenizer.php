<?php

namespace DJEM\Crosslinks;

class Tokenizer
{
    /**
     * Parser states.
     *
     * @var T_TEXT, T_TAG
     */
    const T_TEXT = 0;
    const T_TAG = 1;

    private $text = [];
    private $state = self::T_TEXT;

    private function __construct()
    {
    }

    private function addText($text)
    {
        $this->text = str_split($text);
    }

    private function tokenize()
    {
    }

    public static function parse($text)
    {
        $tokenizer = new self();
        $tokenizer->addText($text);

        return $tokenizer->tokenize();
    }
}
