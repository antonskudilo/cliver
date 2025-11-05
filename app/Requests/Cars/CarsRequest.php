<?php

namespace App\Requests\Cars;

use App\Requests\Common\BaseRequest;

final readonly class CarsRequest extends BaseRequest
{
    /**
     * @param string|null $model
     * @param string|null $number
     * @param string|null $driver
     * @param array|null $driverIds
     * @param int|null $limit
     */
    public function __construct(
        public ?string $model = null,
        public ?string $number = null,
        public ?string $driver = null,
        public ?array  $driverIds = null,
        public ?int    $limit = null
    ) {}

    /**
     * @param array $input
     * @return CarsRequest
     */
    public static function fromArray(array $input): CarsRequest
    {
        if (isset($input['model'])) {
            $model = (string)$input['model'];
        } else {
            $model = null;
        }

        if (isset($input['number'])) {
            $number = (string)$input['number'];
        } else {
            $number = null;
        }

        if (isset($input['driver'])) {
            $driver = (string)$input['driver'];
        } else {
            $driver = null;
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
            model: $model,
            number: $number,
            driver: $driver,
            driverIds: $driverIds,
            limit: $limit
        );
    }
}
