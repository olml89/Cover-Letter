<?php

declare(strict_types=1);

namespace Tests\Unit\PDFCreator;

use olml89\CoverLetter\ErrorHandling\Exceptions\ValidationException;
use olml89\CoverLetter\PDFCreator\Metadata;
use olml89\CoverLetter\Utils\DatetimeImmutable;
use Tests\TestCase;

final class MetadataTest extends TestCase
{
    private readonly DatetimeImmutable $creationDate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->creationDate = new DatetimeImmutable();
    }

    private function createMetadata(DatetimeImmutable $modDate): Metadata
    {
        return new Metadata(
            creationDate: $this->creationDate,
            creator: null,
            keywords: null,
            modDate: $modDate,
            producer: null,
            description: null
        );
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

        $this->createMetadata($modDate);
    }

    public function testItCreatesMetadataIfModDateIsEqualThanCreationDate(): void
    {
        $modDate = clone $this->creationDate;

        $metadata = $this->createMetadata($modDate);

        $this->assertInstanceOf(Metadata::class, $metadata);
        $this->assertEquals($metadata->creationDate, $this->creationDate);
        $this->assertEquals($metadata->modDate, $modDate);
    }

    public function testItThrowsValidationExceptionIfModDateIsGreaterThanCreationDate(): void
    {
        $modDate = $this->creationDate->modify('+1 day');

        $metadata = $this->createMetadata($modDate);

        $this->assertInstanceOf(Metadata::class, $metadata);
        $this->assertEquals($metadata->creationDate, $this->creationDate);
        $this->assertEquals($metadata->modDate, $modDate);
    }
}
