<?php

namespace DJEM\Crosslinks;

class Crosslinks
{
    private $tokens = [];
    private $phrases = [];
    private $stemmers = [];

    private $candidates = [];
    private $buffer = [];

    private function __construct()
    {
    }

    private function addStemmers($stemmers)
    {
        $this->stemmers = $stemmers;
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

        $entries = [];
        foreach ($phrases as $phrase => $link) {
            $entries[] = [
                'phrase' => array_map(function ($word) {
                    return $this->normalizeWord($word);
                }, preg_split('/\s+/', $phrase)),
                'link' => $link,
            ];
        }

        foreach ($entries as $phrase) {
            foreach ($phrase['phrase'] as $word) {
                if (empty($word)) {
                    continue;
                }

                if (! isset($this->phrases[$word])) {
                    $this->phrases[$word] = [];
                }
                $this->phrases[$word][] = $phrase;
            }
        }
    }

    private function normalizeWord($word)
    {
        $word = mb_ereg_replace('\W', '', $word);
        $word = mb_strtolower($word);

        foreach ($this->stemmers as $func) {
            $word = $func($word);
        }

        return $word;
    }

    private function fillCandidates($word)
    {
        // remove invalid candidates
        $this->candidates = array_filter($this->candidates, function ($entry) use ($word) {
            return array_search($word, $entry['phrase']) !== false;
        });

        // insert new candidates
        foreach (isset($this->phrases[$word]) ? $this->phrases[$word] : [] as $phrases) {
            $entry = $phrases;
            $entry['position'] = count($this->buffer);
            $this->candidates[] = $entry;
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
            $matched = false;
            $entry['phrase'] = array_filter($entry['phrase'], function ($phrase) use ($word, &$matched) {
                if ($matched == false && $word == $phrase) {
                    $matched = true;

                    return false;
                }

                return true;
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
        $skipLink = false;

        foreach ($this->tokens as $token) {
            if ($token->type == 'Text') {
                if ($skipLink) {
                    $this->buffer[] = $token;
                    continue;
                }
                $word = $this->normalizeWord($token->text);

                $this->fillCandidates($word);
                $this->buffer[] = $token;

                $this->checkFullPhrase($word);
            } else {
                $this->buffer[] = $token;
                if ($token->type == 'Html') {
                    if (preg_match('|<a\W|i', $token->text)) {
                        $this->candidates = [];
                        $skipLink = true;
                    } elseif (preg_match('|</a\W|i', $token->text)) {
                        $skipLink = false;
                    }
                }
            }
        }

        $this->normalizeBuffer();

        return Tokenizer::toString($this->buffer);
    }

    public static function parse($text, $phrases, $stemmers = [])
    {
        if (! count($phrases)) {
            return $text;
        }

        $crosslinks = new self();

        $crosslinks->addStemmers($stemmers);
        $crosslinks->addText($text);
        $crosslinks->addPhrases($phrases);

        return $crosslinks->run();
    }
}
