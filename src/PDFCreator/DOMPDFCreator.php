<?php

declare(strict_types=1);

namespace olml89\CoverLetter\PDFCreator;

use DateTimeImmutable;
use Dompdf\Dompdf;
use olml89\CoverLetter\Filesystem\Filesystem;
use olml89\CoverLetter\Filesystem\TemplateFile;
use olml89\CoverLetter\Filesystem\WritableFile;
use olml89\CoverLetter\ReplaceableText\Description;
use olml89\CoverLetter\ReplaceableText\Keywords;

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
        $domPdf = new Dompdf();
        $this->addCreatorAndProducer($domPdf, $metadata);

        /*
         * This metadata is set automatically by Dompdf through meta tags in the HTML of the Cover Letter template.
         * https://stackoverflow.com/questions/32233664/what-pdf-meta-data-does-dompdf-support
         *
         * The author meta tag is not set through metadata configuration, but directly in the HTML of the template
         * because it is not intended to change between Cover Letters.
         */
        $template = $template
            ->replace(new Keywords($metadata->keywords ?? ''))
            ->replace(new Description($metadata->description ?? ''));

        $coverLetterFile = $this->createCoverLetterFile($domPdf, $path, $template);
        $this->filesystem->saveWritableFile($coverLetterFile);

        $this->addCreationDateAndModificationDate($path, $metadata->creationDate, $metadata->modDate);

        return $coverLetterFile;
    }

    /**
     * If metadata information is null, we ignore it.
     *
     * We don't need to manually set the commented out metadata because it is set magically by Dompdf
     * from meta tags in the HTML of the Cover Letter template.
     * https://stackoverflow.com/questions/32233664/what-pdf-meta-data-does-dompdf-support
     */
    private function addCreatorAndProducer(Dompdf $domPdf, Metadata $metadata): void
    {
        /**
         * This is set by the <title> HTML tag, so it is not needed
         * $domPdf->addInfo('Title', $metadata->title ?? '');
         */

        /**
         * This is set by the <meta name="author"> tag, so it is not needed
         * $domPdf->addInfo('Author', $metadata->author ?? '');
         */

        /**
         * This somehow does not work if you try to set it up, so we have to use touch()
         * $domPdf->addInfo('CreationDate', $metadata->creationDate ?? '');
         */

        /**
         * This sets up the Content Creator metadata. It will override the Authors metadata too.
         */
        $domPdf->addInfo('Creator', $metadata->creator ?? '');

        /**
         * This is set up by the <meta name="keywords"> tag, so it is not needed
         * $domPdf->addInfo('Keywords', $metadata->keywords ?? '');
         */

        /**
         * This somehow does not work if you try to set it up, so we have to use touch()
         * $domPdf->addInfo('ModDate', $metadata->modDate ?? '');
         */

        /**
         * This sets up the Encoding software metadata
         */
        $domPdf->addInfo('Producer', $metadata->producer ?? '');

        /**
         * This is set up by the <meta name="description"> tag, so it is not needed
         * $domPdf->addInfo('Subject', $metadata->description ?? '')
         */
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
    private function addCreationDateAndModificationDate(
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
