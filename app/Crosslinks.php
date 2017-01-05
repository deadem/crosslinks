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
            return ! ! array_search($word, $entry['phrase']);
        });

        // insert new candidates
        foreach ($this->phrases as $entry) {
            if (array_search($word, $entry['phrase']) !== false) {
                $entry['position'] = count($this->buffer);
                $this->candidates[] = $entry;
            }
        }
    }

    private function checkPhrase($word)
    {
        foreach ($this->candidates as $entry) {
            // remove matched word
            $phrase = array_filter($entry['phrase'], function ($phrase) use ($word) {
                return $word != $phrase;
            });

            // full phrase match
            if (count($phrase) == 0) {
                array_splice($this->buffer, $entry['position'], 0, [
                    (object) [
                        'type' => 'CrosslinkStart',
                        'text' => '<a href="'.htmlentities($entry['link']).'">',
                    ],
                ]);
                $this->buffer[] = (object) [
                    'type' => 'CrosslinkEnd',
                    'text' => '</a>',
                ];

                // only one candidate can match
                $this->candidates = [];
                break;
            }
        }
    }

    private function run()
    {
        $this->buffer = [];
        $this->candidates = [];

        foreach ($this->tokens as $token) {
            if ($token->type == 'Text') {
                $this->fillCandidates($token->text);
                $this->buffer[] = $token;

                $this->checkPhrase($token->text);
            } else {
                $this->buffer[] = $token;
            }
        }

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
