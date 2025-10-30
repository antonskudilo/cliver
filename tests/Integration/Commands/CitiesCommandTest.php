<?php

namespace Tests\Integration\Commands;

use Tests\TestCase;
use Throwable;

class CitiesCommandTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testCommandExecutesSuccessfully(): void
    {
        $output = $this->captureBufferedOutput(fn() => $this->makeApp()->run(['cities', '--limit=1']));
        $this->assertStringContainsString('Cities', $output);
    }
}