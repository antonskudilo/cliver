<?php

namespace App\Exceptions;

use Cliver\Core\Exceptions\ConsoleException;

final class AssertCollectionItemTypeException extends ConsoleException
{
    /**
     * @param string $collectionClass
     * @param string $expectedClass
     * @param string $entityClass
     */
    public function __construct(string $collectionClass, string $expectedClass, string $entityClass)
    {
        parent::__construct(
            sprintf(
                'Cannot add item to %s: expected %s, got %s',
                $collectionClass,
                $expectedClass,
                $entityClass
            )
        );
    }

    /**
     * @param string $collectionClass
     * @param string $expectedClass
     * @param string $entityClass
     * @return self
     */
    public static function for(string $collectionClass, string $expectedClass, string $entityClass): self
    {
        return new self($collectionClass, $expectedClass, $entityClass);
    }
}
