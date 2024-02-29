<?php

declare(strict_types=1);

namespace Tests\Unit\Utils;

use olml89\CoverLetter\Utils\Env;
use Tests\Factories\RandomStringGenerator;
use Tests\TestCase;

final class EnvTest extends TestCase
{
    public function testItReturnsNullIfEnvironmentVariableIsNotFound(): void
    {
        $environmentVariableName = $this->container->get(RandomStringGenerator::class)->generate();

        $this->assertNull(
            Env::get($environmentVariableName)
        );
    }

    public function testItConvertsTrueableValueToTrue(): void
    {
        $_ENV['A'] = 'true';
        $_ENV['B'] = '(true)';

        $this->assertTrue(Env::get('A'));
        $this->assertTrue(Env::get('B'));
    }

    public function testItConvertsFalseableValueToFalse(): void
    {
        $_ENV['A'] = 'false';
        $_ENV['B'] = '(false)';

        $this->assertFalse(Env::get('A'));
        $this->assertFalse(Env::get('B'));
    }

    public function testItConvertsEmptyableValueToEmptyString(): void
    {
        $_ENV['A'] = 'empty';
        $_ENV['B'] = '(empty)';
        $_ENV['C'] = '';

        $this->assertEquals('', Env::get('A'));
        $this->assertEquals('', Env::get('B'));
        $this->assertEquals('', Env::get('C'));
    }

    public function testItConvertsNullableValueToNull(): void
    {
        $_ENV['A'] = 'null';
        $_ENV['B'] = '(null)';

        $this->assertNull(Env::get('A'));
        $this->assertNull(Env::get('B'));
    }

    public function testItGetsEnvironmentVariable(): void
    {
        $environmentVariableName = $this->container->get(RandomStringGenerator::class)->generate();
        $environmentVariableValue = $this->container->get(RandomStringGenerator::class)->generate();
        $_ENV[$environmentVariableName] = $environmentVariableValue;

        $this->assertEquals(
            $environmentVariableValue,
            Env::get($environmentVariableName)
        );
    }

    public function testItReturnsDefaultValueIfEnvironmentVariableIsNull(): void
    {
        $environmentVariableName = $this->container->get(RandomStringGenerator::class)->generate();
        $_ENV[$environmentVariableName] = 'null';
        $defaultValue = $this->container->get(RandomStringGenerator::class)->generate();

        $this->assertNull(
            Env::get($environmentVariableName)
        );
        $this->assertEquals(
            $defaultValue,
            Env::get($environmentVariableName, $defaultValue)
        );
    }
}
