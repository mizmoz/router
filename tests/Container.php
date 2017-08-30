<?php

namespace Mizmoz\Router\Tests;

use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    /**
     * @var array
     */
    private $items = [];

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
    public function get($id)
    {
        return (isset($this->items[$id]) ? $this->items[$id] : new $id);
    }

    /**
     * @inheritDoc
     */
    public function has($id)
    {
        return isset($this->items[$id]);
    }
}