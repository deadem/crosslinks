<?php

namespace DJEM\Crosslinks;

class Crosslinks
{
    private $text = [];
    private $phrases = [];

    private function __construct()
    {
    }

    private function addText($text)
    {
        $this->text = str_split($text);
    }

    private function addPhrases($phrases)
    {
        if (! is_array($phrases)) {
            return;
        }

        $this->phrases = $phrases;
    }

    private function run()
    {
    }

    public static function parse($text, $phrases)
    {
        $crosslinks = new self();

        $crosslinks->addText($text);
        $crosslinks->addPhrases($phrases);

        return $crosslinks->run();
    }
}
