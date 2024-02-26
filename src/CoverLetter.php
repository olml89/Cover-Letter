<?php

declare(strict_types=1);

namespace olml89\CoverLetter;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Dotenv\Dotenv;
use olml89\CoverLetter\ErrorHandling\ErrorHandler;
use olml89\CoverLetter\ErrorHandling\ErrorHandlerBootstrapper;
use olml89\CoverLetter\PDFCreator\DOMPDFCreator;
use olml89\CoverLetter\PDFCreator\Metadata;
use olml89\CoverLetter\PDFCreator\PDFCreator;
use olml89\CoverLetter\ReplaceableText\Company;
use olml89\CoverLetter\ReplaceableText\Position;
use olml89\CoverLetter\Utils\Command;
use function DI\create;
use function DI\factory;

final readonly class CoverLetter
{
    public function __construct(
        private CoverLetterCreator $coverLetterCreator,
    ) {}

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public static function bootstrap(): self
    {
        self::bootstrapEnvironment(dirname(__DIR__));
        self::bootstrapErrorHandler();
        $coverLetterCreator = self::bootstrapCoverLetterCreator();

        return new self($coverLetterCreator);
    }

    private static function bootstrapEnvironment(string $path): void
    {
        $dotEnv = Dotenv::createImmutable($path);
        $dotEnv->load();
    }

    private static function bootstrapErrorHandler(): void
    {
        $errorHandler = new ErrorHandler();
        new ErrorHandlerBootstrapper($errorHandler);
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    private static function bootstrapCoverLetterCreator(): CoverLetterCreator
    {
        $container = new Container([
            PDFCreator::class => create(DOMPDFCreator::class),
            Metadata::class => factory(
                fn (): Metadata => Metadata::fromPath('./config/metadata.php')
            ),
            Configuration::class => factory(
                fn (): Configuration => Configuration::fromPath('./config/config.php')
            ),
        ]);

        return $container->get(CoverLetterCreator::class);
    }

    public function create(array $argv): Command
    {
        $coverLetterFilePath = $this->coverLetterCreator->create(
            Position::fromInput($argv[1] ?? null),
            Company::fromInput($argv[2] ?? null),
        );

        return Command::success($coverLetterFilePath);
    }
}
