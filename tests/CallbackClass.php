<?php

namespace Mizmoz\Router\Tests;

use function GuzzleHttp\Psr7\stream_for;
use Mizmoz\Router\Contract\Parser\ResultInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class CallbackClass
{
    const RESPONSE = 'callback-called';

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param ResultInterface $result
     * @return ResponseInterface
     */
    public function callThis(
        RequestInterface $request,
        ResponseInterface $response,
        ResultInterface $result
    ): ResponseInterface
    {
        return $response->withBody(stream_for(self::RESPONSE));
    }
}