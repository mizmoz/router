<?php

namespace Mizmoz\Router\Exception;

use Psr\Http\Message\ServerRequestInterface;

class RouteNotFoundException extends RuntimeException
{
    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * Get the request
     *
     * @return ServerRequestInterface
     */
    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    /**
     * Set the request
     *
     * @param ServerRequestInterface $request
     * @return RouteNotFoundException
     */
    public function setRequest(ServerRequestInterface $request): RouteNotFoundException
    {
        $this->request = $request;
        return $this;
    }
}