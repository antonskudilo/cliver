<?php

namespace Tests\Integration\Commands;

use Tests\TestCase;
use Throwable;

class OrdersCommandTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testCommandExecutesSuccessfully(): void
    {
        $output = $this->captureBufferedOutput(fn() => $this->makeApp()->run(['orders', '--limit=1']));
        $this->assertStringContainsString('Orders', $output);
    }
}