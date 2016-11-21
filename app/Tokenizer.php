<?php

namespace DJEM\Crosslinks;

class Tokenizer
{
    private $state = ['Text'];

    private $current = 0;
    private $length = 0;

    private $lastIndex = 0;

    private $text = [];
    private $tokens = [];

    private function __construct()
    {
    }

    private function setState($state)
    {
        $this->state[] = $state;
    }

    private function popState()
    {
        return array_pop($this->state);
    }

    private function getState()
    {
        return end($this->state);
    }

    private function isSpace()
    {
        $char = $this->text[$this->current];

        return preg_match('/[[:space:]]/', $char) || $char == '';
    }

    private function isAmp()
    {
        return $this->text[$this->current] == '&';
    }

    private function isPunct()
    {
        return preg_match('/[[:punct:]]/', $this->text[$this->current]);
    }

    private function current($char)
    {
        return $this->text[$this->current] == $char;
    }

    private function isAhead($string)
    {
        $length = strlen($string);
        $text = implode('', array_slice($this->text, $this->current, $length));

        return strcasecmp($string, $text) == 0;
    }

    private function getTokenText($offset = 0)
    {
        return implode('', array_slice($this->text, $this->lastIndex, $this->current - $this->lastIndex + $offset));
    }

    private function addToken($offset = 0)
    {
        if (is_string($offset)) {
            $offset = strlen($offset);
        }

        if ($this->lastIndex >= $this->current + $offset || $this->current + $offset > $this->length) {
            return;
        }

        $token = [
            'type' => $this->getState(),
            'text'  => $this->getTokenText($offset),
        ];

        $this->lastIndex = $this->current = $this->current + $offset;
        $this->tokens[] = $token;
    }

    private function addText($text)
    {
        $this->text = str_split($text);
    }

    private function tokenize()
    {
        $this->current = 0;
        $this->length = count($this->text);
        $this->tokens = [];

        while ($this->current < $this->length) {
            call_user_func([$this, 'parse'.$this->getState()]);
        }
        $this->addToken();

        return $this->tokens;
    }

    private function openTag()
    {
        if ($this->isAhead('<!--')) {
            $this->setState('Comment');
        } elseif ($this->isAhead('<script')) {
            $this->setState('Script');
        } elseif ($this->isAhead('<style')) {
            $this->setState('Style');
        } else {
            $this->setState('Html');
        }
    }

    private function parseText()
    {
        if ($this->current('<')) {
            $this->addToken();
            $this->openTag();
        } elseif ($this->isSpace()) {
            $this->addToken();
            $this->setState('TextSpace');
        } elseif ($this->isAmp()) {
            $this->addToken();
            $this->setState('TextAmp');
        } elseif ($this->isPunct()) {
            $this->addToken();
            $this->setState('TextPunct');
        }

        ++$this->current;
    }

    private function parseTextAmp()
    {
        if ($this->current(';')) {
            $this->addToken(';');
            $this->popState();

            return;
        } elseif ($this->current('<')) {
            $this->addToken();
            $this->popState();
            $this->openTag();
        }

        ++$this->current;
    }

    private function parseTextPunct()
    {
        if ($this->current('<')) {
            $this->addToken();
            $this->popState();
            $this->openTag();
        } elseif ($this->isSpace()) {
            $this->addToken();
            $this->popState();
            $this->setState('TextSpace');
        } elseif ($this->isAmp()) {
            $this->addToken();
            $this->popState();
            $this->setState('TextAmp');
        } elseif (! $this->isPunct()) {
            $this->addToken();
            $this->popState();
        }
        ++$this->current;
    }

    private function parseTextSpace()
    {
        if ($this->current('<')) {
            $this->addToken();
            $this->popState();
            $this->openTag();
        } elseif ($this->isAmp()) {
            $this->addToken();
            $this->popState();
            $this->setState('TextAmp');
        } elseif ($this->isPunct()) {
            $this->addToken();
            $this->popState();
            $this->setState('TextPunct');
        } elseif (! $this->isSpace()) {
            $this->addToken();
            $this->popState();
        }
        ++$this->current;
    }

    private function parseHtml()
    {
        if ($this->current('"')) {
            $this->setState('HtmlQuote');
        } elseif ($this->current('\'')) {
            $this->setState('HtmlSingleQuote');
        } elseif ($this->current('>')) {
            $this->addToken('>');
            $this->popState();

            return;
        }

        ++$this->current;
    }

    private function parseHtmlQuote()
    {
        if ($this->current('"')) {
            $this->popState();
        }

        ++$this->current;
    }

    private function parseHtmlSingleQuote()
    {
        if ($this->current('\'')) {
            $this->popState();
        }

        ++$this->current;
    }

    private function parseComment()
    {
        if ($this->current('-') && $this->isAhead('-->')) {
            $this->addToken('-->');
            $this->popState();

            return;
        }
        ++$this->current;
    }

    public static function parse($text)
    {
        $tokenizer = new self();
        $tokenizer->addText($text);

        return $tokenizer->tokenize();
    }
}
