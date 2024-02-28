<?php

declare(strict_types=1);

namespace Tests\Unit\PDFCreator;

use olml89\CoverLetter\ErrorHandling\Exceptions\ValidationException;
use olml89\CoverLetter\PDFCreator\Metadata;
use olml89\CoverLetter\Utils\DateTimeImmutable;
use Tests\TestCase;

final class MetadataTest extends TestCase
{
    private readonly DateTimeImmutable $creationDate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->creationDate = new DateTimeImmutable();
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
