<?php

declare(strict_types=1);

namespace Tests;

use DI\Container;
use olml89\CoverLetter\Application;
use olml89\CoverLetter\ErrorHandling\ErrorHandlerManager;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected Container $container;

    protected static function bootApplication(): Container
    {
        return Application::bootstrap()->getContainer();
    }

    protected function setUp(): void
    {
        $this->container = self::bootApplication();
    }

    /**
     * Returns an entry of the container after setting it up.
     *
     * @template T
     * @param class-string<T> $abstract Entry name or a class name.
     * @param mixed $instance
     *
     * @return T
     */
    protected function getInstance(string $abstract, mixed $instance): mixed
    {
        $this->container->set($abstract, $instance);

        return $instance;
    }

    protected function tearDown(): void
    {
        $this
            ->container
            ->get(ErrorHandlerManager::class)
            ->shutdown();
    }
}
