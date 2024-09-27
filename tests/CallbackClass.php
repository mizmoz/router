<?php

namespace Mizmoz\Router\Tests;

use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CallbackClass
{
    const string RESPONSE = 'callback-called';

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
        return $response->withBody(Utils::streamFor(self::RESPONSE));
    }
}