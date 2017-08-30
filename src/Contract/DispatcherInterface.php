<?php

namespace Mizmoz\Router\Contract;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface DispatcherInterface
{
    /**
     * Dispatch the request
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function dispatch(RequestInterface $request): ResponseInterface;
}