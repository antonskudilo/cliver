<?php

namespace Tests\Integration\Commands;

use Tests\TestCase;
use Throwable;

class DriversCommandTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testCommandExecutesSuccessfully(): void
    {
        $output = $this->captureBufferedOutput(fn() => $this->makeApp()->run(['drivers', '--limit=1']));
        $this->assertStringContainsString('Drivers', $output);
    }
}