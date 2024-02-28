<?php

declare(strict_types=1);

namespace Tests\Unit\IO;

use olml89\CoverLetter\ErrorHandling\Exceptions\ValidationException;
use olml89\CoverLetter\IO\Input;
use Tests\Factories\RandomStringGenerator;
use Tests\TestCase;

final class InputTest extends TestCase
{
    private readonly RandomStringGenerator $randomStringGenerator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->randomStringGenerator = $this->container->get(RandomStringGenerator::class);
    }

    public function testItThrowsValidationExceptionIfArgumentsAreLessThanExpected(): void
    {
        $argv = [
            $this->randomStringGenerator->generate(), // The first one is the command name
            $this->randomStringGenerator->generate(),
            $this->randomStringGenerator->generate(),
        ];
        $missingArgumentName = $this->randomStringGenerator->generate();

        $this->expectExceptionObject(
            ValidationException::missingArgument($missingArgumentName)
        );

        Input::read(
            $argv,
            $this->randomStringGenerator->generate(),
            $this->randomStringGenerator->generate(),
            $missingArgumentName,
        );
    }

    public function tstItThrowsValidationExceptionIfAnUnexistingArgumentIsRequested(): void
    {
        $input = new Input([
            $this->randomStringGenerator->generate() => $this->randomStringGenerator->generate(),
            $this->randomStringGenerator->generate() => $this->randomStringGenerator->generate(),
        ]);
        $unexistingArgumentName = $this->randomStringGenerator->generate();

        $this->expectExceptionObject(
            ValidationException::missingArgument($unexistingArgumentName)
        );

        $input->get($unexistingArgumentName);
    }
}
