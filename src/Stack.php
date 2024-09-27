<?php

namespace Mizmoz\Router;

use Mizmoz\Router\Contract\MiddlewareInterface;
use Mizmoz\Router\Contract\StackInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Stack implements StackInterface
{
    /**
     * @var array<StackInterface|MiddlewareInterface>
     */
    private array $stack = [];

    /**
     * @inheritDoc
     */
    public function addMiddleware(MiddlewareInterface $middleware): StackInterface
    {
        $this->stack[] = $middleware;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addStack(StackInterface $stack, bool $append = true): StackInterface
    {
        $this->stack = ($append
            ? array_merge($this->stack, $stack->toArray())
            : array_merge($stack->toArray(), $this->stack)
        );
        return $this;
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
        return $this($request, $response);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (! $this->stack) {
            return $response;
        }

        while ($middleware = array_shift($this->stack)) {
            /** @var MiddlewareInterface $middleware */
            $response = $middleware->process($request, $response, $this);
        }

        return $response;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return $this->stack;
    }
}