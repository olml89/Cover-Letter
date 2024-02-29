<?php

declare(strict_types=1);

namespace Tests\Unit\Utils;

use DateTimeInterface;
use DateTimeZone;
use InvalidArgumentException;
use olml89\CoverLetter\Utils\DateTimeImmutable;
use Tests\TestCase;

final class DateTimeImmutableTest extends TestCase
{
    public function testItInstantiatesFromCurrentTimeAndUTC(): void
    {
        $now = new \DateTimeImmutable(
            'now',
            new DateTimeZone('UTC')
        );
        $dateTime = new DateTimeImmutable();

        $this->assertEquals(
            $now->format(DateTimeInterface::ATOM),
            (string)$dateTime
        );
    }

    public function testItInstantiatesToNullFromNullDatetimeString(): void
    {
        $this->assertNull(DateTimeImmutable::create(null));
    }

    public function testItThrowsExceptionIfTryingToInstantiateWithAnInvalidFormat(): void
    {
        $datetime = (new DateTimeImmutable())->format(DateTimeInterface::RFC1123);

        $this->expectExceptionObject(
            new InvalidArgumentException(sprintf(
                'Error using the datetime \'%s\' to create DatetimeImmutable with format \'%s\'',
                $datetime,
                DateTimeInterface::ATOM,
            ))
        );

        DateTimeImmutable::create($datetime);
    }

    public function testItInstantiatesFromDatetimeString(): void
    {
        $datetime = (new DateTimeImmutable())->format(DateTimeInterface::ATOM);

        $dateTime = DateTimeImmutable::create($datetime);

        $this->assertEquals(
            $datetime,
            (string)$dateTime
        );
    }
}
