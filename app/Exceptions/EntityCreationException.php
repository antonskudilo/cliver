<?php

namespace App\Exceptions;

use Cliver\Core\Exceptions\ConsoleException;

final class EntityCreationException extends ConsoleException
{
    /**
     * @param string $entityClass
     * @param array<string,mixed> $data
     */
    public function __construct(string $entityClass, array $data)
    {
        parent::__construct(
            sprintf(
                'Cannot create entity %s from data: %s',
                $entityClass,
                json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            )
        );
    }

    /**
     * @param string $entityClass
     * @param array $data
     * @return self
     */
    public static function for(string $entityClass, array $data): self
    {
        return new self($entityClass, $data);
    }
}
