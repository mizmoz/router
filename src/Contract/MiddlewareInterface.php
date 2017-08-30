<?php

namespace Mizmoz\Router\Contract;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface MiddlewareInterface
{
    /**
     * Process the middleware
     * 
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param MiddlewareInterface|null $next
     * @return ResponseInterface
     */
    public function process(
        RequestInterface $request,
        ResponseInterface $response,
        MiddlewareInterface $next = null
    ): ResponseInterface;
}