<?php

namespace Tests\Integration\Commands;

use Tests\TestCase;
use Throwable;

class OrdersStatisticCommandTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function testCommandExecutesSuccessfully(): void
    {
        $output = $this->captureBufferedOutput(fn() => $this->makeApp()->run(['orders_statistic']));
        $this->assertStringContainsString('Orders statistics', $output);
    }
}