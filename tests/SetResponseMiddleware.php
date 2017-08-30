<?php

namespace Mizmoz\Router\Tests;

use Mizmoz\Router\Contract\MiddlewareInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class SetResponseMiddleware implements MiddlewareInterface
{
    /**
     * @var string
     */
    private $header;

    /**
     * @var string
     */
    private $value;

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
        RequestInterface $request,
        ResponseInterface $response,
        MiddlewareInterface $next = null
    ): ResponseInterface
    {
        $response = $response->withAddedHeader($this->header, $this->value);
        return ($next ? $next->process($request, $response) : $response);
    }
}