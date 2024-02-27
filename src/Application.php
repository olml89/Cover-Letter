<?php

declare(strict_types=1);

namespace olml89\CoverLetter;

use DI\Container;
use Dotenv\Dotenv;
use olml89\CoverLetter\ErrorHandling\ErrorHandler;
use olml89\CoverLetter\ErrorHandling\ErrorHandlerManager;
use olml89\CoverLetter\Filesystem\DiskFilesystem;
use olml89\CoverLetter\Filesystem\Filesystem;
use olml89\CoverLetter\PDFCreator\DOMPDFCreator;
use olml89\CoverLetter\PDFCreator\Metadata;
use olml89\CoverLetter\PDFCreator\PDFCreator;
use Psr\Container\ContainerInterface;
use function DI\create;
use function DI\factory;
use function DI\get;

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
            ErrorHandlerManager::class => create()->constructor(create(ErrorHandler::class)),

            // Need to bind the interfaces to the default implementations
            Filesystem::class => create(DiskFilesystem::class),
            PDFCreator::class => create(DOMPDFCreator::class)->constructor(get(Filesystem::class)),

            // Need to load these value objects providing a scalar value which is the path to grab values from.
            // Won't be created until CoverLetterCreator is needed.
            Metadata::class => factory(
                fn (): Metadata => Metadata::fromPath()
            ),
            Configuration::class => factory(
                fn (ContainerInterface $container): Configuration => Configuration::fromPath(
                    $container->get(Filesystem::class)
                )
            ),
        ]);
    }
}
