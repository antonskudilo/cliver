<?php

namespace Tests;

use Cliver\Core\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->getContainer(dirname(__FILE__, 2));
    }
}
