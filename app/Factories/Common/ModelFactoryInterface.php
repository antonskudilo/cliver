<?php

namespace App\Factories\Common;

/**
 * @template T
 */
interface ModelFactoryInterface
{
    /**
     * @param array $data
     * @return T
     */
    public function fromArray(array $data): object;
}
