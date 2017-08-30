<?php

namespace Mizmoz\Router\Contract;

interface StackInterface extends MiddlewareInterface
{
    /**
     * Add a middleware item to the stack
     *
     * @param MiddlewareInterface $middleware
     * @return StackInterface
     */
    public function addMiddleware(MiddlewareInterface $middleware): StackInterface;

    /**
     * Add a stack to this stack
     *
     * @param StackInterface $stack
     * @param bool $append
     * @return StackInterface
     */
    public function addStack(StackInterface $stack, bool $append = true): StackInterface;

    /**
     * Get the stack as an array
     *
     * @return array
     */
    public function toArray(): array;
}