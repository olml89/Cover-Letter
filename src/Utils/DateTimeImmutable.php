<?php

declare(strict_types=1);

namespace olml89\CoverLetter\Utils;

use DateTimeInterface;
use DateTimeZone;
use InvalidArgumentException;
use Stringable;

final class DateTimeImmutable extends \DateTimeImmutable implements Stringable
{
    private const string FORMAT = DateTimeInterface::ATOM;
    private const string TIMEZONE = 'UTC';

    /**
     * Hack to avoid initializing with weird milliseconds and type 3 TimeZone if calling directly the constructor.
     */
    public function __construct()
    {
        $now = new \DateTimeImmutable(
            datetime: 'now',
            timezone: new DateTimeZone(self::TIMEZONE)
        );

        parent::__construct(
            datetime: $now->format(self::FORMAT),
            timezone: $now->getTimezone()
        );
    }

    public static function create(?string $datetime): ?self
    {
        if (is_null($datetime)) {
            return null;
        }

        return self::createFromFormat(
            self::FORMAT,
            $datetime,
            new DateTimeZone(self::TIMEZONE),
        ) ?: throw new InvalidArgumentException(sprintf(
            'Error using the datetime \'%s\' to create DatetimeImmutable with format \'%s\'',
            $datetime,
            self::FORMAT,
        ));
    }

    public function __toString()
    {
        return parent::format(self::FORMAT);
    }
}
