<?php

namespace Blablacar\Twig\TokenParser;

use Blablacar\Twig\Node\StampNode;

class StampTokenParser extends \Twig_TokenParser
{
    public function parse(\Twig_Token $token)
    {
        $lineno   = $token->getLine();
        $stream   = $this->parser->getStream();

        // 'svg'
        $name = $stream->expect(\Twig_Token::STRING_TYPE)->getValue();

        // %} (from {% stamp %})
        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        $aboveDumps = [];
        while (true) {

            // everything above {% stamp_dump %}
            $aboveDumps[] = $this->parser->subparse(function(\Twig_Token $token) {
                return $token->test('stamp_dump');
            });

            // allow nested {% stamp %} usage using distinct names
            $dumpName = $stream->next() && $stream->expect(\Twig_Token::STRING_TYPE)->getValue();
            if ($dumpName == $name) {
                break ;
            }
        }

        // %} (from {% stamp_dump %})
        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        // everything below {% stamp_dump %}
        $belowDump = $this->parser->subparse(function(\Twig_Token $token) {
            return $token->test('endstamp');
        });

        // %} (from {% endstamp %})
        $stream->next() && $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        return new StampNode($name, $aboveDumps, $belowDump, $lineno, $this->getTag());
    }

    public function getTag()
    {
        return 'stamp';
    }
}
