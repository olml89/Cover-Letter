<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;

final class DummyTest extends TestCase
{
    public function testTrue(): void
    {
        $this->assertTrue(true);
    }
}
