<?php

namespace App\Requests\Drivers;

use App\Requests\Common\BaseRequest;

final readonly class DriversRequest extends BaseRequest
{
    /**
     * @param array|null $driverIds
     * @param array|null $orderCityIds
     * @param array|null $orderDate
     * @param string|null $phone
     * @param string|null $name
     * @param int|null $limit
     */
    public function __construct(
        public ?array  $driverIds = null,
        public ?array  $orderCityIds = null,
        public ?array  $orderDate = null,
        public ?string $phone = null,
        public ?string $name = null,
        public ?int    $limit = null
    ) {}

    /**
     * @param array $input
     * @return DriversRequest
     */
    public static function fromArray(array $input): DriversRequest
    {
        if (isset($input['drivers'])) {
            $driverIds = array_map('intval', (array)$input['drivers']);
        } else {
            $driverIds = null;
        }

        if (isset($input['order_city'])) {
            $orderCityIds = array_map('intval', (array)$input['order_city']);
        } else {
            $orderCityIds = null;
        }

        if (isset($input['order_date'])) {
            $orderDate = (array)$input['order_date'];
        } else {
            $orderDate = null;
        }

        if (isset($input['phone'])) {
            $phone = (string)$input['phone'];
        } else {
            $phone = null;
        }

        if (isset($input['name'])) {
            $name = (string)$input['name'];
        } else {
            $name = null;
        }

        if (isset($input['limit'])) {
            $limit = (int) $input['limit'];
        } else {
            $limit = null;
        }

        return new DriversRequest(
            driverIds: $driverIds,
            orderCityIds: $orderCityIds,
            orderDate: $orderDate,
            phone: $phone,
            name: $name,
            limit: $limit
        );
    }
}
