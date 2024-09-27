<?php

namespace Mizmoz\Router\Tests;

use Mizmoz\Router\Contract\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class SetResponseMiddleware implements MiddlewareInterface
{
    /**
     * @var string
     */
    private string $header;

    /**
     * @var string
     */
    private string $value;

    /**
     * Set a header on the response object
     *
     * @param string $header
     * @param string $value
     */
    public function __construct(string $header, string $value)
    {
        $this->header = $header;
        $this->value = $value;
    }

    /**
     * @inheritDoc
     */
    public function process(
        ServerRequestInterface $request,
        ResponseInterface $response,
        MiddlewareInterface $next = null
    ): ResponseInterface
    {
        $response = $response->withAddedHeader($this->header, $this->value);
        return $next ? $next->process($request, $response) : $response;
    }
}