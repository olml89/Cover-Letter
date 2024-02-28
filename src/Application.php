<?php

declare(strict_types=1);

namespace olml89\CoverLetter;

use DI\Container;
use Dotenv\Dotenv;
use JetBrains\PhpStorm\NoReturn;
use olml89\CoverLetter\ErrorHandling\ErrorHandler;
use olml89\CoverLetter\ErrorHandling\ErrorHandlerManager;
use olml89\CoverLetter\Filesystem\DiskFilesystem;
use olml89\CoverLetter\Filesystem\Filesystem;
use olml89\CoverLetter\IO\Input;
use olml89\CoverLetter\PDFCreator\DOMPDFCreator;
use olml89\CoverLetter\PDFCreator\Metadata;
use olml89\CoverLetter\PDFCreator\PDFCreator;
use function DI\create;
use function DI\factory;
use function DI\get;

final readonly class Application
{
    public function __construct(
        private Container $container,
    ) {}

    public static function bootstrap(): self
    {
        self::bootstrapEnvironment(dirname(__DIR__));
        $errorHandlerManager = self::bootErrorHandlerManager();
        $container = self::bootstrapContainer($errorHandlerManager);

        return new self($container);
    }

    private static function bootstrapEnvironment(string $path): void
    {
        $dotEnv = Dotenv::createImmutable($path);
        $dotEnv->load();
    }

    public static function bootErrorHandlerManager(): ErrorHandlerManager
    {
        return new ErrorHandlerManager(new ErrorHandler());
    }

    private static function bootstrapContainer(ErrorHandlerManager $errorHandlerManager): Container
    {
        return new Container([
            // Needed to be able to call the shutdown method from the outside tear downing tests.
            // Also, we need it to instantiate it right now in order to catch exceptions, not to lazy load it.
            ErrorHandlerManager::class => $errorHandlerManager,

            // Need to bind the interfaces to the default implementations
            Filesystem::class => create(DiskFilesystem::class),
            PDFCreator::class => create(DOMPDFCreator::class)->constructor(get(Filesystem::class)),

            // Need to load these value objects providing a scalar value which is the path to grab values from.
            // Won't be created until CoverLetterCreator is needed.
            Metadata::class => factory(
                fn (): Metadata => Metadata::fromPath()
            ),
            Configuration::class => factory(
                fn (Container $container): Configuration => Configuration::fromPath(
                    $container->get(Filesystem::class)
                )
            ),
        ]);
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    #[NoReturn]
    public function execute(Input $input): void
    {
        $result = $this
            ->container
            ->get(CreateCoverLetter::class)
            ->create($input);

        // Set off custom error handlers
        $this
            ->container
            ->get(ErrorHandlerManager::class)
            ->shutdown();

        echo $result->message;
        exit($result->status);
    }
}
