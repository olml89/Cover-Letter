<?php

declare(strict_types=1);

namespace Tests\Unit\Filesystem;

use Mockery;
use olml89\CoverLetter\Filesystem\Directory;
use olml89\CoverLetter\Filesystem\Filesystem;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Container\ContainerInterface;
use Stringable;
use Tests\Factories\RandomStringGenerator;
use Tests\TestCase;

final class DirectoryTest extends TestCase
{
    private readonly RandomStringGenerator $randomStringGenerator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->randomStringGenerator = $this->container->get(RandomStringGenerator::class);
    }

    public static function provideSubPath(): array
    {
        $randomStringGenerator = self::bootApplication()->get(RandomStringGenerator::class);

        return [
            'string' => [
                $randomStringGenerator->generate(),
            ],
            'stringable' => [
                new readonly class($randomStringGenerator->generate()) implements Stringable
                {
                    public function __construct(
                        private string $string,
                    ) {}

                    public function __toString(): string
                    {
                        return $this->string;
                    }
                }
            ],
        ];
    }

    public function testItReturnsDirectoryPathIfNoSubPathIsAppended(): void
    {
        $directoryPath = $this->randomStringGenerator->generate();

        $filesystem = $this->getInstance(
            Filesystem::class,
            Mockery::mock(Filesystem::class, function (Mockery\MockInterface $mock): void {})
        );

        $directory = new Directory(
            $filesystem,
            $directoryPath,
        );

        $path = $directory->getPath();

        $this->assertEquals($directoryPath, $path);
    }

    #[DataProvider('provideSubPath')]
    public function testItReturnsDirectoryPathWithSubPathAppended(string|Stringable $subPath): void
    {
        $directoryPath = $this->randomStringGenerator->generate();

        $filesystem = $this->getInstance(
            Filesystem::class,
            Mockery::mock(Filesystem::class, function (Mockery\MockInterface $mock): void {})
        );

        $directory = new Directory(
            $filesystem,
            $directoryPath,
        );

        $path = $directory->getPath($subPath);

        $this->assertEquals(
            sprintf('%s/%s', $directoryPath, $subPath),
            $path
        );
    }

    #[DataProvider('provideSubPath')]
    public function testItCreatesSubdirectory(string|Stringable $subPath): void
    {
        $directoryPath = $this->randomStringGenerator->generate();
        $expectedSubdirectoryPath = sprintf('%s/%s', $directoryPath, $subPath);

        $expectedSubdirectoryManager = new class($this->container, $expectedSubdirectoryPath)
        {
            private ?Directory $expectedSubdirectory = null;

            public function __construct(
                private readonly ContainerInterface $container,
                private readonly string $expectedSubdirectoryPath,
            ) {}

            public function get(): Directory
            {
                return $this->expectedSubdirectory ??= $this->generate(
                    $this->container->get(Filesystem::class),
                    $this->expectedSubdirectoryPath
                );
            }

            private function generate(Filesystem $filesystem, string $expectedSubdirectoryPath): Directory
            {
                return new Directory(
                    $filesystem,
                    $expectedSubdirectoryPath
                );
            }
        };

        $filesystem = $this->getInstance(
            Filesystem::class,
            Mockery::mock(
                Filesystem::class,
                function (Mockery\MockInterface $mock) use ($expectedSubdirectoryPath, $expectedSubdirectoryManager): void {
                    $mock
                        ->shouldReceive('createDirectory')
                        ->once()
                        ->with($expectedSubdirectoryPath)
                        ->andReturn(
                            $expectedSubdirectoryManager->get()
                        );
                }
            )
        );

        $directory = new Directory(
            $filesystem,
            $directoryPath
        );

        $subDirectory = $directory->createSubdirectory($subPath);

        $this->assertEquals(
            $expectedSubdirectoryManager->get(),
            $subDirectory
        );
    }
}
