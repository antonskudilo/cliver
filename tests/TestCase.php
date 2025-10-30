<?php

namespace Tests;

use App\IO\BufferedOutput;
use App\IO\OutputInterface;
use Cliver\Core\Testing\TestCase as BaseTestCase;
use Throwable;

abstract class TestCase extends BaseTestCase
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->getContainer(dirname(__FILE__, 2));
    }

    /**
     * @param callable $callback
     * @return string
     * @throws Throwable
     */
    protected function captureBufferedOutput(callable $callback): string
    {
        $output = new BufferedOutput();
        $this->container->bind(OutputInterface::class, $output);

        try {
            $callback();
        } finally {
            return $output->getBuffer();
        }
    }
}
