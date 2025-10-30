<?php

namespace App\Requests\Drivers;

use App\Requests\Common\BaseRequest;

final readonly class DriversRequest extends BaseRequest
{
    /**
     * @param array|null $driverIds
     * @param int|null $limit
     */
    public function __construct(
        public ?array $driverIds = null,
        public ?int $limit = null
    ) {}

    /**
     * @param array $input
     * @return DriversRequest
     */
    public static function fromArray(array $input): DriversRequest
    {
        if (isset($input['drivers'])) {
            $driverIds = array_map('intval', (array) $input['drivers']);
        } else {
            $driverIds = null;
        }

        if (isset($input['limit'])) {
            $limit = (int) $input['limit'];
        } else {
            $limit = null;
        }

        return new DriversRequest(
            driverIds: $driverIds,
            limit: $limit
        );
    }
}
