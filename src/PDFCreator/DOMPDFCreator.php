<?php

declare(strict_types=1);

namespace olml89\CoverLetter\PDFCreator;

use Dompdf\Dompdf;
use olml89\CoverLetter\Filesystem\Filesystem;
use olml89\CoverLetter\Filesystem\TemplateFile;
use olml89\CoverLetter\Filesystem\WritableFile;
use olml89\CoverLetter\ReplaceableText\Description;
use olml89\CoverLetter\ReplaceableText\Keywords;
use olml89\CoverLetter\Utils\DateTimeImmutable;

final readonly class DOMPDFCreator implements PDFCreator
{
    public function __construct(
        private Filesystem $filesystem,
    ) {}

    /**
     * @throws OutputCreationException
     */
    public function create(string $path, Metadata $metadata, TemplateFile $template): WritableFile
    {
        // Modifications of the metadata in the Dompdf renderer
        $domPdf = $this->addMetadataToPdfRenderer(
            $metadata->creator,
            $metadata->producer,
            $metadata->creationDate,
            $metadata->modDate
        );
        // Modifications of the metadata through HTML tags, automatically rendered by Dompdf
        $template = $this->addMetadataThroughHtmlTags(
            $template,
            $metadata->keywords,
            $metadata->description
        );

        $coverLetterFile = $this->createCoverLetterFile($domPdf, $path, $template);
        $this->filesystem->saveWritableFile($coverLetterFile);

        // Modifications of the metadata through the manipulation of the filesystem (touch())
        $this->addMetadataThroughFilesystemManipulation(
            $path,
            $metadata->creationDate,
            $metadata->modDate
        );

        return $coverLetterFile;
    }

    /**
     * We don't need to manually set the commented out metadata because it is set magically by Dompdf
     * from meta tags in the HTML of the Cover Letter template.
     * https://stackoverflow.com/questions/32233664/what-pdf-meta-data-does-dompdf-support
     */
    private function addMetadataToPdfRenderer(
        ?string $creator,
        ?string $producer,
        ?DateTimeImmutable $creationDate,
        ?DateTimeImmutable $modDate,
    ): Dompdf {
        $domPdf = new Dompdf();

        /**
         * Title
         * This is set by the <title> HTML tag, so it is not needed
         * $domPdf->addInfo('Title', $metadata->title ?? '');
         */

        /**
         * Author
         * This is set by the <meta name="author"> tag, so it is not needed
         * $domPdf->addInfo('Author', $metadata->author ?? '');
         */

        /**
         * CreationDate
         * If it is null we skip setting it, so the renderer fills it with the current system time.
         * (This somehow does not work, so we have to use touch() too).
         */
        if (!is_null($creationDate)) {
            $domPdf->addInfo('CreationDate', (string)$creationDate);
        }

        /**
         * Creator
         * This sets up the Content Creator metadata.
         * (It will override the Authors metadata too).
         */
        $domPdf->addInfo('Creator', $creator ?? '');

        /**
         * Keywords
         * This is set up by the <meta name="keywords"> tag, so it is not needed
         * $domPdf->addInfo('Keywords', $metadata->keywords ?? '');
         */

        /**
         * ModDate
         * If it is null we skip setting it, so the renderer fills it with the current system time.
         * (This somehow does not work, so we have to use touch() too).
         */
        if (!is_null($modDate)) {
            $domPdf->addInfo('ModDate', (string)$modDate);
        }

        /**
         * Producer
         * This sets up the Encoding software metadata
         */
        $domPdf->addInfo('Producer', $producer ?? '');

        /**
         * Subject
         * This is set up by the <meta name="description"> tag, so it is not needed
         * (This will override Title metadata if it is not set)
         * $domPdf->addInfo('Subject', $metadata->description ?? '')
         */

        return $domPdf;
    }

    /*
     * This metadata is set automatically by Dompdf through meta tags in the HTML of the Cover Letter template.
     * https://stackoverflow.com/questions/32233664/what-pdf-meta-data-does-dompdf-support
     *
     * The author meta tag (Authors metadata) and the title html tag (Title metadata) are not set through metadata
     * configuration, but directly in the HTML of the template because they are not intended to change
     * between Cover Letters.
     */
    private function addMetadataThroughHtmlTags(TemplateFile $template, ?string $keywords, ?string $description): TemplateFile
    {
        return $template
            ->replace(new Keywords($keywords ?? ''))
            ->replace(new Description($description ?? ''));
    }

    private function createCoverLetterFile(Dompdf $domPdf, string $path, TemplateFile $template): WritableFile
    {
        $domPdf->loadHtml($template->content);
        $domPdf->render();

        return new WritableFile(
            path: $path,
            content: $domPdf->output()
        );
    }

    /**
     * touch() will basically set both creationDate and modDate on a first call, and on subsequent calls will update
     * the modDate. accessDate would be modified if passed as a second parameter to touch().
     */
    private function addMetadataThroughFilesystemManipulation(
        string $path,
        ?DateTimeImmutable $creationDate,
        ?DateTimeImmutable $modDate,
    ): void {
        // If modDate is null, this touch call will also set up modDate
        if (!is_null($creationDate)) {
            touch($path, $creationDate->getTimestamp());
        }

        // If creationDate is null, this touch call will also set up creationDate
        if (!is_null($modDate)) {
            touch($path, $modDate->getTimestamp());
        }
    }
}
