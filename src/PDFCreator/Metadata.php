<?php

declare(strict_types=1);

namespace olml89\CoverLetter\PDFCreator;

use olml89\CoverLetter\ErrorHandling\Exceptions\ValidationException;
use olml89\CoverLetter\Utils\DateTimeImmutable;
use olml89\CoverLetter\Utils\RequiresArrayConfigurationFile;

final readonly class Metadata
{
    use RequiresArrayConfigurationFile;

    private const string PATH = __DIR__ . '/../../config/metadata.php';

    /**
     * @throws ValidationException
     */
    public function __construct(
        public ?DateTimeImmutable $creationDate,
        public ?string $creator,
        public ?string $keywords,
        public ?DateTimeImmutable $modDate,
        public ?string $producer,
        public ?string $description,
    ) {
        if (!is_null($this->creationDate) && !is_null($this->modDate) && ($this->modDate < $this->creationDate)) {
            throw new ValidationException(sprintf(
        'modDate date \'%s\' cannot be earlier in time than creationDate \'%s\'',
                $this->modDate,
                $this->creationDate,
            ));
        }
    }

    public static function fromPath(): self
    {
        $data = self::requireArrayConfigurationFile(self::PATH);

        return new self(
            creationDate: DateTimeImmutable::create($data['creationDate']),
            creator: $data['creator'],
            keywords: $data['keywords'],
            modDate: DateTimeImmutable::create($data['modDate']),
            producer: $data['producer'],
            description: $data['description'],
        );
    }
}
