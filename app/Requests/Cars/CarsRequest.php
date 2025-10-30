<?php

namespace App\Requests\Cars;

use App\Requests\Common\BaseRequest;

final readonly class CarsRequest extends BaseRequest
{
    /**
     * @param string|null $name
     * @param string|null $number
     * @param array|null $driverIds
     * @param int|null $limit
     */
    public function __construct(
        public ?string $name = null,
        public ?string $number = null,
        public ?array $driverIds = null,
        public ?int $limit = null
    ) {}

    /**
     * @param array $input
     * @return CarsRequest
     */
    public static function fromArray(array $input): CarsRequest
    {
        if (isset($input['name'])) {
            $name = (string)$input['name'];
        } else {
            $name = null;
        }

        if (isset($input['number'])) {
            $number = (string)$input['number'];
        } else {
            $number = null;
        }

        if (isset($input['driverIds'])) {
            $driverIds = array_map('intval', (array)$input['driverIds']);
        } else {
            $driverIds = null;
        }

        if (isset($input['limit'])) {
            $limit = (int)$input['limit'];
        } else {
            $limit = null;
        }

        return new CarsRequest(
            name: $name,
            number: $number,
            driverIds: $driverIds,
            limit: $limit
        );
    }
}
