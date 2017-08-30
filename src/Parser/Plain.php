<?php

namespace Mizmoz\Router\Parser;

use Mizmoz\Router\Contract\Parser\ResultInterface;

class Plain extends ParserBase
{
    /**
     * @inheritDoc
     */
    public function match(string $uri): ResultInterface
    {
        if ($uri === $this->match) {
            return (new Result(Result::MATCH_FULL));
        }

        // remove the first slash and split the uris to attempt to match each part
        $matchParts = explode('/', substr($this->match, 1));
        $uriParts = explode('/', substr($uri, 1));

        if (count($matchParts) > count($uriParts)) {
            // match is longer than current uri so not a match
            return new Result();
        }

        $matched = [];
        $variables = [];

        foreach ($matchParts as $key => $matchPart) {
            if (! trim($matchPart)) {
                // just a slash, so not a match
                continue;
            }

            if ($matchPart === '*') {
                // wildcard match
                $matched = $uriParts;
                break;
            }

            // keep the parts we've matched
            $matched[] = $uriParts[$key];

            if ($matchPart === $uriParts[$key]) {
                // parts are equal so a match
                continue;
            }

            if (strpos($matchPart, ':') === 0) {
                // variable match
                $variables[substr($matchPart, 1)] = $uriParts[$key];
                continue;
            }

            // failed to match
            return new Result();
        }

        if (count($matched) === count($uriParts)) {
            // full match
            return (new Result(Result::MATCH_FULL))->setVariables($variables);
        }

        // partial match, return the part of the uri that still needs to be matched
        $uri = ($this->match === '/' ? $uri : substr($uri, strlen(implode('/', $matched)) + 1));
        return (new Result(Result::MATCH_PARTIAL))
            ->setUri($uri)
            ->setVariables($variables);
    }
}