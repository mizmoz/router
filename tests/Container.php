<?php

namespace Mizmoz\Router\Tests;

use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    /**
     * @var array
     */
    private array $items;

    /**
     * Container constructor.
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * @inheritDoc
     */
    public function get($id): mixed
    {
        return $this->items[$id] ?? new $id;
    }

    /**
     * @inheritDoc
     */
    public function has($id): bool
    {
        return isset($this->items[$id]);
    }
}