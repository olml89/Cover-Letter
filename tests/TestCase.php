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
        return Application::bootstrap();
    }

    protected function setUp(): void
    {
        $this->container = self::bootApplication();
    }

    protected function tearDown(): void
    {
        $this
            ->container
            ->get(ErrorHandlerManager::class)
            ->shutdown();
    }
}
