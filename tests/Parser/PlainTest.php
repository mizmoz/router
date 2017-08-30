<?php

namespace Mizmoz\Router\Tests\Parser;

use Mizmoz\Router\Parser\Plain;
use Mizmoz\Router\Parser\Result;
use Mizmoz\Router\Tests\TestCase;

class PlainTest extends TestCase
{
    public function testSimpleMatch()
    {
        $result = (new Plain('/'))->match('/');
        $this->assertEquals(Result::MATCH_FULL, $result->match());
    }

    public function testPartialMatch()
    {
        $result = (new Plain('/'))->match('/app');
        $this->assertEquals(Result::MATCH_PARTIAL, $result->match());
        $this->assertEquals('/app', $result->getUri());
    }

    public function testLongPartialMatch()
    {
        $result = (new Plain('/app'))->match('/app/users');
        $this->assertEquals(Result::MATCH_PARTIAL, $result->match());
        $this->assertEquals('/users', $result->getUri());
    }

    public function testNotPartialMatch()
    {
        $result = (new Plain('/app'))->match('/application');
        $this->assertEquals(Result::MATCH_NONE, $result->match());
    }

    public function testMatchWithVariable()
    {
        $result = (new Plain('/user/:userId'))->match('/user/123');
        $this->assertEquals(Result::MATCH_FULL, $result->match());
        $this->assertEquals([
            'userId' => '123',
        ], $result->getVariables());
    }

    public function testMatchWithMultipleVariables()
    {
        $result = (new Plain('/user/:userId/:action'))->match('/user/123/edit');
        $this->assertEquals(Result::MATCH_FULL, $result->match());
        $this->assertEquals([
            'userId' => '123',
            'action' => 'edit',
        ], $result->getVariables());
    }

    public function testMatchWithMultipleVariablesPartialMatch()
    {
        $result = (new Plain('/user/:userId'))->match('/user/123/edit');
        $this->assertEquals(Result::MATCH_PARTIAL, $result->match());
        $this->assertEquals([
            'userId' => '123',
        ], $result->getVariables());
    }

    public function testMatchWildcard()
    {
        $result = (new Plain('/*'))->match('/app');
        $this->assertEquals(Result::MATCH_FULL, $result->match());
    }

    public function testNoMatchWildcard()
    {
        $result = (new Plain('/app/*'))->match('/application/cheese');
        $this->assertEquals(Result::MATCH_NONE, $result->match());
    }
}