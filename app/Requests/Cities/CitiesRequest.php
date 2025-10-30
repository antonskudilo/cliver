<?php

namespace App\Requests\Cities;

use App\Requests\Common\BaseRequest;

final readonly class CitiesRequest extends BaseRequest
{
    /**
     * @param string|null $name
     * @param int|null $limit
     */
    public function __construct(
        public ?string $name = null,
        public ?int $limit = null
    ) {}

    /**
     * @param array $input
     * @return CitiesRequest
     */
    public static function fromArray(array $input): CitiesRequest
    {
        if (isset($input['name'])) {
            $name = (string) $input['name'];
        } else {
            $name = null;
        }

        if (isset($input['limit'])) {
            $limit = (int) $input['limit'];
        } else {
            $limit = null;
        }

        return new CitiesRequest(
            name: $name,
            limit: $limit
        );
    }
}
