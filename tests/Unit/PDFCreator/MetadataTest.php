<?php

declare(strict_types=1);

namespace Tests\Unit\PDFCreator;

use Mockery;
use olml89\CoverLetter\ErrorHandling\Exceptions\ValidationException;
use olml89\CoverLetter\Filesystem\Filesystem;
use olml89\CoverLetter\PDFCreator\Metadata;
use olml89\CoverLetter\Utils\DateTimeImmutable;
use ReflectionClass;
use Tests\Factories\RandomStringGenerator;
use Tests\TestCase;

final class MetadataTest extends TestCase
{
    private readonly RandomStringGenerator $randomStringGenerator;
    private readonly DateTimeImmutable $creationDate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->randomStringGenerator = $this->container->get(RandomStringGenerator::class);
        $this->creationDate = new DateTimeImmutable();
    }

    public function testItLoadsMetadataFromMetaFile(): void
    {
        $metaFilePath = realpath((new ReflectionClass(Metadata::class))->getConstant('PATH'));

        $meta = [
            'creation_date' => (string)$this->creationDate,
            'creator' => $this->randomStringGenerator->generate(),
            'keywords' => $this->randomStringGenerator->generate(),
            'mod_date' => (string)$this->creationDate,
            'producer' => $this->randomStringGenerator->generate(),
            'description' => $this->randomStringGenerator->generate(),
        ];

        $filesystem = $this->getInstance(
            Filesystem::class,
            Mockery::mock(
                Filesystem::class,
                function (Mockery\MockInterface $mock) use ($metaFilePath, $meta): void {
                    $mock
                        ->shouldReceive('require')
                        ->once()
                        ->with($metaFilePath)
                        ->andReturn($meta);
                }
            )
        );

        $metadata = Metadata::fromPath($filesystem);

        $this->assertEquals($meta['creation_date'], (string)$metadata->creationDate);
        $this->assertEquals($meta['creator'], $metadata->creator);
        $this->assertEquals($meta['keywords'], $metadata->keywords);
        $this->assertEquals($meta['mod_date'], (string)$metadata->modDate);
        $this->assertEquals($meta['producer'], $metadata->producer);
        $this->assertEquals($meta['description'], $metadata->description);
    }

    public function testItThrowsValidationExceptionIfModDateIsLowerThanCreationDate(): void
    {
        $modDate = $this->creationDate->modify('-1 day');

        $this->expectExceptionObject(
            new ValidationException(sprintf(
                'modDate date \'%s\' cannot be earlier in time than creationDate \'%s\'',
                $modDate,
                $this->creationDate,
            ))
        );

        new Metadata(
            creationDate: $this->creationDate,
            modDate: $modDate,
        );
    }

    public function testItCreatesMetadataIfModDateIsEqualThanCreationDate(): void
    {
        $modDate = clone $this->creationDate;

        $metadata = new Metadata(
            creationDate: $this->creationDate,
            modDate: $modDate,
        );

        $this->assertInstanceOf(Metadata::class, $metadata);
        $this->assertEquals($metadata->creationDate, $this->creationDate);
        $this->assertEquals($metadata->modDate, $modDate);
    }

    public function testItThrowsValidationExceptionIfModDateIsGreaterThanCreationDate(): void
    {
        $modDate = $this->creationDate->modify('+1 day');

        $metadata = new Metadata(
            creationDate: $this->creationDate,
            modDate: $modDate,
        );

        $this->assertInstanceOf(Metadata::class, $metadata);
        $this->assertEquals($metadata->creationDate, $this->creationDate);
        $this->assertEquals($metadata->modDate, $modDate);
    }
}
