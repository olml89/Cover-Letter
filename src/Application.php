<?php

declare(strict_types=1);

namespace olml89\CoverLetter;

use DI\Container;
use Dotenv\Dotenv;
use olml89\CoverLetter\ErrorHandling\ErrorHandler;
use olml89\CoverLetter\ErrorHandling\ErrorHandlerManager;
use olml89\CoverLetter\PDFCreator\DOMPDFCreator;
use olml89\CoverLetter\PDFCreator\Metadata;
use olml89\CoverLetter\PDFCreator\PDFCreator;
use function DI\create;
use function DI\factory;

final class Application
{
    public static function bootstrap(): Container
    {
        self::bootstrapEnvironment(dirname(__DIR__));

        return self::bootstrapContainer();
    }

    private static function bootstrapEnvironment(string $path): void
    {
        $dotEnv = Dotenv::createImmutable($path);
        $dotEnv->load();
    }

    private static function bootstrapContainer(): Container
    {
        return new Container([
            // Needed to be able to call the shutdown method from the outside tear downing tests
            ErrorHandlerManager::class => create()->constructor(new ErrorHandler()),

            // Need to bind the interface to the default implementation
            PDFCreator::class => create(DOMPDFCreator::class),

            // Need to load these value objects providing a scalar value which is the path to grab values from
            Metadata::class => factory(
                fn (): Metadata => Metadata::fromPath('./config/metadata.php')
            ),
            Configuration::class => factory(
                fn (): Configuration => Configuration::fromPath('./config/config.php')
            ),
        ]);
    }
}
