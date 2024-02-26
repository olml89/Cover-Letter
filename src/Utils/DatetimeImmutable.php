<?php

declare(strict_types=1);

namespace olml89\CoverLetter\Utils;

use DateTimeZone;
use InvalidArgumentException;
use Stringable;

final class DatetimeImmutable extends \DateTimeImmutable implements Stringable
{
    private const string FORMAT = 'Y/m/d H:i:s';
    private const string TIMEZONE = 'UTC';

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
