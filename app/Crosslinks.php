<?php

namespace DJEM\Crosslinks;

class Crosslinks
{
    private $tokens = [];
    private $phrases = [];

    private $candidates = [];
    private $buffer = [];

    private function __construct()
    {
    }

    private function addText($text)
    {
        $this->tokens = Tokenizer::parse($text);
    }

    private function addPhrases($phrases)
    {
        $this->phrases = [];
        if (! is_array($phrases)) {
            return;
        }

        foreach ($phrases as $phrase => $link) {
            $this->phrases[] = [
                'phrase' => preg_split('/\s+/', $phrase),
                'link' => $link,
            ];
        }
    }

    private function fillCandidates($word)
    {
        // remove invalid candidates
        $this->candidates = array_filter($this->candidates, function ($entry) use ($word) {
            return array_search($word, $entry['phrase']) !== false;
        });

        // insert new candidates
        foreach ($this->phrases as $entry) {
            if (array_search($word, $entry['phrase']) !== false) {
                $entry['position'] = count($this->buffer);
                $this->candidates[] = $entry;
            }
        }
    }

    private function getStartToken($link)
    {
        return (object) [
            'type' => 'CrosslinkStart',
            'text' => '<a href="'.htmlentities($link).'">',
        ];
    }

    private function getEndToken()
    {
        return (object) [
            'type' => 'CrosslinkEnd',
            'text' => '</a>',
        ];
    }

    private function checkFullPhrase($word)
    {
        foreach ($this->candidates as &$entry) {
            // remove matched word
            $entry['phrase'] = array_filter($entry['phrase'], function ($phrase) use ($word) {
                return $word != $phrase;
            });

            // full phrase match
            if (count($entry['phrase']) == 0) {
                array_splice($this->buffer, $entry['position'], 0, [$this->getStartToken($entry['link'])]);
                $this->buffer[] = $this->getEndToken();

                // only one candidate can match
                $this->candidates = [];
                break;
            }
        }
    }

    private function isTextToken($token)
    {
        switch ($token->type) {
            case 'Text':
            case 'TextPunct':
            case 'TextSpace':
            case 'TextAmp':
                return true;

            default:
                return false;
        }
    }

    private function normalizeBuffer()
    {
        $temp = [];
        $lastLinkState = false;
        $isTokenOpen = false;

        foreach ($this->buffer as $token) {
            if ($lastLinkState) {
                if (! $isTokenOpen && $token->type == 'Text') {
                    $temp[] = $lastLinkState;
                    $isTokenOpen = true;
                } elseif ($isTokenOpen && ! $this->isTextToken($token)) {
                    $temp[] = $this->getEndToken();
                    $isTokenOpen = false;
                }
            }
            if ($token->type == 'CrosslinkStart') {
                $lastLinkState = $token;
                $isTokenOpen = true;
            }
            if ($token->type == 'CrosslinkEnd') {
                $lastLinkState = false;

                // already closed
                if ($isTokenOpen == false) {
                    continue;
                }
                $isTokenOpen = false;
            }
            $temp[] = $token;
        }

        $this->buffer = $temp;
    }

    private function run()
    {
        $this->buffer = [];
        $this->candidates = [];

        foreach ($this->tokens as $token) {
            if ($token->type == 'Text') {
                $this->fillCandidates($token->text);
                $this->buffer[] = $token;

                $this->checkFullPhrase($token->text);
            } else {
                $this->buffer[] = $token;
            }
        }

        $this->normalizeBuffer();

        return Tokenizer::toString($this->buffer);
    }

    public static function parse($text, $phrases)
    {
        $crosslinks = new self();

        $crosslinks->addText($text);
        $crosslinks->addPhrases($phrases);

        return $crosslinks->run();
    }
}
