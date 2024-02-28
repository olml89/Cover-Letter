<?php

declare(strict_types=1);

namespace Tests\Unit\PDFCreator;

use DOMDocument;
use olml89\CoverLetter\Filesystem\Filesystem;
use olml89\CoverLetter\Filesystem\TemplateFile;
use olml89\CoverLetter\Filesystem\WritableFile;
use olml89\CoverLetter\PDFCreator\DOMPDFCreator;
use olml89\CoverLetter\PDFCreator\Metadata;
use olml89\CoverLetter\Utils\DateTimeImmutable;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\Attributes\DataProvider;
use Smalot\PdfParser\Parser;
use Tests\Factories\RandomStringGenerator;
use Tests\TestCase;

final class DOMPDFCreatorTest extends TestCase
{
    private readonly TemplateFile $templateFile;
    private readonly string $author;
    private readonly string $title;
    private readonly string $coverLetterFilePath;
    private readonly DOMPDFCreator $pdfCreator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->templateFile = $this
            ->container
            ->get(Filesystem::class)
            ->getTemplateFile(__DIR__ . '/../../Fixtures/cover_letter_template.html');

        /**
         * Title and Author are always static, hardcoded in the HTML Template.
         * We do this in order to dynamically retrieve them from there instead of hard coding them here in the test.
         */
        $htmlParser = new DOMDocument();
        $htmlParser->loadHTML($this->templateFile->content);

        $this->author = $htmlParser
            ->getElementById('authorTag')
            ->getAttribute('content');

        $this->title = $htmlParser
            ->getElementById('titleTag')
            ->nodeValue;

        $this->coverLetterFilePath = vfsStream::url(sprintf(
            '%s/%s.pdf',
            vfsStream::setup()->path(),
            $this->container->get(RandomStringGenerator::class)->generate()
        ));

        $this->pdfCreator = $this->container->get(DOMPDFCreator::class);
    }

    private static function createMetadata(
        ?DateTimeImmutable $creationDate = null,
        ?string            $creator = null,
        ?string            $producer = null,
        ?DateTimeImmutable $modDate = null,
        ?string            $description = null,
        ?string            $keywords = null,
    ): Metadata {
        return new Metadata(
            creationDate: $creationDate,
            creator: $creator,
            keywords: $keywords,
            modDate: $modDate,
            producer: $producer,
            description: $description
        );
    }

    private function parsePdfDetails(WritableFile $coverLetterFile): array
    {
        return (new Parser())
            ->parseContent($coverLetterFile->content)
            ->getDetails();
    }

    public static function provideStaticMetadata(): array
    {
        $randomStringGenerator = self::bootApplication()->get(RandomStringGenerator::class);

        return [
            'not null static metadata' => [
                self::createMetadata(
                    creator: $randomStringGenerator->generate(),
                    producer: $randomStringGenerator->generate(),
                    description: $randomStringGenerator->generate(),
                    keywords: $randomStringGenerator->generate(),
                )
            ],
            'null static metadata' => [
                self::createMetadata(),
            ],
        ];
    }

    public function testItCreatesCoverLetterFile(): void
    {
        $coverLetterFile = $this->pdfCreator->create(
            $this->coverLetterFilePath,
            self::createMetadata(),
            $this->templateFile
        );

        $this->assertFileExists($coverLetterFile->path);
    }

    #[DataProvider('provideStaticMetadata')]
    public function testItAddsCreatorAndProducer(Metadata $metadata): void
    {
        $coverLetterFile = $this->pdfCreator->create(
            $this->coverLetterFilePath,
            $metadata,
            $this->templateFile
        );

        $details = $this->parsePdfDetails($coverLetterFile);

        // Comparison of Title and Authors, statically set through HTML tags
        $this->assertEquals(
            $this->author,
            $details['Author']
        );
        $this->assertEquals(
            $this->title,
            $details['Title']
        );

        // Comparison of Creator, Producer, Keywords and Subject
        $this->assertEquals(
            $metadata->creator,
            $details['Creator']
        );
        $this->assertEquals(
            $metadata->producer,
            $details['Producer']
        );
        $this->assertEquals(
            $metadata->keywords,
            $details['Keywords']
        );
        $this->assertEquals(
            $metadata->description,
            $details['Subject']
        );
    }

    public function testItAddsCurrentSystemTimeAsCreationDateAndModDateIfBothAreNull(): void
    {
        $metadata = self::createMetadata();

        $coverLetterFile = $this->pdfCreator->create(
            $this->coverLetterFilePath,
            $metadata,
            $this->templateFile
        );

        $details = $this->parsePdfDetails($coverLetterFile);

        // Compare values on the PDF metadata
        $this->assertEquals(
            $details['CreationDate'],
            $details['ModDate']
        );
        $this->assertInstanceOf(
            DateTimeImmutable::class,
            DateTimeImmutable::create($details['CreationDate'])
        );
        $this->assertInstanceOf(
            DateTimeImmutable::class,
            DateTimeImmutable::create($details['ModDate'])
        );

        // Compare values on the filesystem
        $this->assertEquals(
            DateTimeImmutable::create($details['CreationDate']),
            (new DateTimeImmutable())->setTimestamp(filectime($this->coverLetterFilePath))
        );
        $this->assertEquals(
            DateTimeImmutable::create($details['ModDate']),
            (new DateTimeImmutable())->setTimestamp(filemtime($this->coverLetterFilePath))
        );
    }

    public function testItAddsModDateAsCreationDateAndModDateIfCreationDateIsNull(): void
    {
        $modDate = new DateTimeImmutable();
        $metadata = self::createMetadata(modDate: $modDate);

        $coverLetterFile = $this->pdfCreator->create(
            $this->coverLetterFilePath,
            $metadata,
            $this->templateFile
        );

        $details = $this->parsePdfDetails($coverLetterFile);

        // Compare values on the PDF metadata
        $this->assertEquals(
            $modDate,
            DateTimeImmutable::create($details['CreationDate'])
        );
        $this->assertEquals(
            $modDate,
            DateTimeImmutable::create($details['ModDate'])
        );

        // Compare values on the filesystem
        $this->assertEquals(
            $modDate,
            (new DateTimeImmutable())->setTimestamp(filectime($this->coverLetterFilePath))
        );
        $this->assertEquals(
            $modDate,
            (new DateTimeImmutable())->setTimestamp(filemtime($this->coverLetterFilePath))
        );
    }

    public function testItAddsCreationDateAsCreationDateAndModDateIfModDateIsNull(): void
    {
        $creationDate = new DateTimeImmutable();
        $metadata = self::createMetadata(creationDate: $creationDate);

        $coverLetterFile = $this->pdfCreator->create(
            $this->coverLetterFilePath,
            $metadata,
            $this->templateFile
        );

        $details = $this->parsePdfDetails($coverLetterFile);

        // Compare values on the PDF metadata
        $this->assertEquals(
            $creationDate,
            DateTimeImmutable::create($details['CreationDate'])
        );
        $this->assertEquals(
            $creationDate,
            DateTimeImmutable::create($details['ModDate'])
        );

        // Compare values on the filesystem
        $this->assertEquals(
            $creationDate,
            (new DateTimeImmutable())->setTimestamp(filectime($this->coverLetterFilePath))
        );
        $this->assertEquals(
            $creationDate,
            (new DateTimeImmutable())->setTimestamp(filemtime($this->coverLetterFilePath))
        );
    }

    public function testItAddsCreationDateAndModDate(): void
    {
        $creationDate = new DateTimeImmutable();
        $modDate = $creationDate->modify('+1 day');

        $metadata = self::createMetadata(
            creationDate: $creationDate,
            modDate: $modDate,
        );

        $coverLetterFile = $this->pdfCreator->create(
            $this->coverLetterFilePath,
            $metadata,
            $this->templateFile
        );

        $details = $this->parsePdfDetails($coverLetterFile);

        // Compare values on the PDF metadata
        $this->assertEquals(
            $creationDate,
            DateTimeImmutable::create($details['CreationDate'])
        );
        $this->assertEquals(
            $modDate,
            DateTimeImmutable::create($details['ModDate'])
        );

        // Compare values on the filesystem
        $this->assertEquals(
            $creationDate,
            (new DateTimeImmutable())->setTimestamp(filectime($this->coverLetterFilePath))
        );
        $this->assertEquals(
            $modDate,
            (new DateTimeImmutable())->setTimestamp(filemtime($this->coverLetterFilePath))
        );
    }

    public function tearDown(): void
    {
        unlink($this->coverLetterFilePath);
    }
}
