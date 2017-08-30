<?php

namespace Mizmoz\Router\Tests;

use function GuzzleHttp\Psr7\stream_for;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CallbackClass
{
    const RESPONSE = 'callback-called';

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function callThis(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface
    {
        return $response->withBody(stream_for(self::RESPONSE));
    }
}