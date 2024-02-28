<?php

declare(strict_types=1);

namespace Tests\Unit\Filesystem;

use olml89\CoverLetter\ErrorHandling\Exceptions\InputReadingException;
use olml89\CoverLetter\ErrorHandling\Exceptions\OutputCreationException;
use olml89\CoverLetter\Filesystem\Directory;
use olml89\CoverLetter\Filesystem\DiskFilesystem;
use olml89\CoverLetter\Filesystem\WritableFile;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use Tests\Factories\Filesystem\TemplateFileFactory;
use Tests\Factories\RandomStringGenerator;
use Tests\TestCase;

/**
 * This test makes use of the bovigo/vfsStream package to mock the filesystem
 */
final class DiskFilesystemTest extends TestCase
{
    private readonly RandomStringGenerator $randomStringGenerator;
    private readonly TemplateFileFactory $templateFileFactory;
    private readonly DiskFilesystem $diskFilesystem;
    private readonly vfsStreamDirectory $root;

    protected function setUp(): void
    {
        parent::setUp();

        $this->randomStringGenerator = $this->container->get(RandomStringGenerator::class);
        $this->templateFileFactory = $this->container->get(TemplateFileFactory::class);
        $this->diskFilesystem = $this->container->get(DiskFilesystem::class);
        $this->root = vfsStream::setup();
    }

    public function testItThrowsInputReadingExceptionIfDirectoryDoesNotExist(): void
    {
        $path = $this->randomStringGenerator->generate();

        $this->expectExceptionObject(
            InputReadingException::dir($path)
        );

        $this->diskFilesystem->getDirectory($path);
    }

    public function testItThrowsInputReadingExceptionIfDirectoryIsNotReadable(): void
    {
        $directoryName = $this->randomStringGenerator->generate();

        // 0000: No rwx permissions for anyone
        vfsStream::newDirectory($directoryName)
            ->at($this->root)
            ->chmod(0000);

        $path = vfsStream::url(
            sprintf(
                '%s/%s',
                $this->root->path(),
                $directoryName,
            )
        );

        $this->expectExceptionObject(
            InputReadingException::dir($path)
        );

        $this->diskFilesystem->getDirectory($path);
    }

    public function testItGetsDirectory(): void
    {
        $directoryName = $this->randomStringGenerator->generate();

        // 0777: rwx permissions for everyone
        vfsStream::newDirectory($directoryName)
            ->at($this->root)
            ->chmod(0777);

        $path = vfsStream::url(
            sprintf(
                '%s/%s',
                $this->root->path(),
                $directoryName,
            )
        );

        $directory = $this->diskFilesystem->getDirectory($path);

        $this->assertEquals(
            new Directory($this->diskFilesystem, $path),
            $directory
        );
    }

    public function testItGetsDirectoryInsteadOfCreatingItIfAlreadyExistsAndIsReadable(): void
    {
        $directoryName = $this->randomStringGenerator->generate();

        // 0777: rwx permissions for everyone
        vfsStream::newDirectory($directoryName)
            ->at($this->root)
            ->chmod(0777);

        $path = vfsStream::url(
            sprintf(
                '%s/%s',
                $this->root->path(),
                $directoryName,
            )
        );

        $directory = $this->diskFilesystem->createDirectory($path);

        $this->assertEquals(
            new Directory($this->diskFilesystem, $path),
            $directory
        );
    }

    public function testItThrowsInputReadingExceptionIfDirectoryAlreadyExistsButIsNotReadable(): void
    {
        $directoryName = $this->randomStringGenerator->generate();

        // 0000: No rwx permissions for anyone
        vfsStream::newDirectory($directoryName)
            ->at($this->root)
            ->chmod(0000);

        $path = vfsStream::url(
            sprintf(
                '%s/%s',
                $this->root->path(),
                $directoryName,
            )
        );

        $this->expectExceptionObject(
            InputReadingException::dir($path)
        );

        $this->diskFilesystem->createDirectory($path);
    }

    public function testItThrowsOutputCreationExceptionIfDirectoryCannotBeCreated(): void
    {
        $directoryName = $this->randomStringGenerator->generate();

        // 0000: No rwx permissions for anyone
        $this->root->chmod(0000);

        $path = vfsStream::url(
            sprintf(
                '%s/%s',
                $this->root->path(),
                $directoryName,
            )
        );

        $this->expectExceptionObject(
            OutputCreationException::dir($path)
        );

        $this->diskFilesystem->createDirectory($path);
    }

    public function testItCreatesDirectory(): void
    {
        $directoryName = $this->randomStringGenerator->generate();

        $path = vfsStream::url(
            sprintf(
                '%s/%s',
                $this->root->path(),
                $directoryName,
            )
        );

        $directory = $this->diskFilesystem->createDirectory($path);

        $this->assertEquals(
            new Directory($this->diskFilesystem, $path),
            $directory
        );

        $this->assertTrue($this->root->hasChild($directoryName));
    }

    public function testItThrowsInputReadingExceptionIfFileDoesNotExist(): void
    {
        $fileName = $this->randomStringGenerator->generate();

        $path = vfsStream::url(
            sprintf(
                '%s/%s',
                $this->root->path(),
                $fileName,
            )
        );

        $this->expectExceptionObject(
            InputReadingException::file($path)
        );

        $this->diskFilesystem->getTemplateFile($path);
    }

    public function testItThrowsInputReadingExceptionIfFileIsNotReadable(): void
    {
        $fileName = $this->randomStringGenerator->generate();

        // 0000: No permissions for anyone
        vfsStream::newFile($fileName)
            ->at($this->root)
            ->chmod(0000);

        $path = vfsStream::url(
            sprintf(
                '%s/%s',
                $this->root->path(),
                $fileName,
            )
        );

        $this->expectExceptionObject(
            InputReadingException::file($path)
        );

        $this->diskFilesystem->getTemplateFile($path);
    }

    public function testItThrowsInputReadingExceptionIfFileContentsAreNotAvailable(): void
    {
        /**
         * https://www.php.net/manual/en/function.file-get-contents.php
         *
         * An E_WARNING level error is generated if filename length is less than zero (and it will not return false
         * for is_dir() or is_readable()
         */
        $fileName = '';

        // 0777: rwx permissions for everyone
        vfsStream::newFile($fileName)
            ->at($this->root)
            ->chmod(0777);

        $path = vfsStream::url(
            sprintf(
                '%s/%s',
                $this->root->path(),
                $fileName,
            )
        );

        $this->expectExceptionObject(
            InputReadingException::fileContent($path)
        );

        $this->diskFilesystem->getTemplateFile($path);
    }

    public function testItGetsTemplateFile(): void
    {
        $fileName = $this->randomStringGenerator->generate();
        $existingTemplateFile = $this->templateFileFactory->generate();

        // 0777: rwx permissions for everyone
        vfsStream::newFile($fileName)
            ->at($this->root)
            ->setContent($existingTemplateFile->content)
            ->chmod(0777);

        $path = vfsStream::url(
            sprintf(
                '%s/%s',
                $this->root->path(),
                $fileName,
            )
        );

        $template = $this->diskFilesystem->getTemplateFile($path);

        $this->assertEquals($existingTemplateFile, $template);
    }

    public function testItThrowsOutputCreationExceptionIfWritableFileCannotBeWritten(): void
    {
        $path = vfsStream::url(
            sprintf(
                '%s/%s',
                $this->root->path(),
                $this->randomStringGenerator->generate()
            )
        );

        $writableFile = new WritableFile(
            path: $path,
            content: $this->randomStringGenerator->generate()
        );

        // https://github.com/bovigo/vfs-stream-examples/tree/master/src/part03
        vfsStream::setQuota(0);

        $this->expectExceptionObject(
            OutputCreationException::file($path)
        );

        $this->diskFilesystem->saveWritableFile($writableFile);
    }

    public function testItSavesWritableFile(): void
    {
        $fileName = $this->randomStringGenerator->generate();

        $path = vfsStream::url(
            sprintf(
                '%s/%s',
                $this->root->path(),
                $fileName,
            )
        );

        $writableFile = new WritableFile(
            path: $path,
            content: $this->randomStringGenerator->generate()
        );

        $this->diskFilesystem->saveWritableFile($writableFile);

        $this->assertTrue($this->root->hasChild($fileName));
    }
}
