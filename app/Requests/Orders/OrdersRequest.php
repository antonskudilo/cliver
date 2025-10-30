<?php

namespace App\Requests\Orders;

use App\Enums\SortDirectionEnum;
use App\Requests\Common\BaseRequest;
use DateTime;
use DateTimeInterface;
use InvalidArgumentException;
use Throwable;

final readonly class OrdersRequest extends BaseRequest
{
    /**
     * @param array|null $driverIds
     * @param array|null $cityIds
     * @param DateTimeInterface|null $date
     * @param int|null $limit
     * @param string|null $orderBy
     * @param SortDirectionEnum|null $direction
     */
    public function __construct(
        public ?array $driverIds = null,
        public ?array $cityIds = null,
        public ?DateTimeInterface $date = null,
        public ?int $limit = null,
        public ?string $orderBy = null,
        public ?SortDirectionEnum $direction = null,
    ) {}

    /**
     * @param array $input
     * @return OrdersRequest
     * @throws Throwable
     */
    public static function fromArray(array $input): OrdersRequest
    {
        if (isset($input['drivers'])) {
            $driverIds = array_map('intval', (array) $input['drivers']);
        } else {
            $driverIds = null;
        }

        if (isset($input['cities'])) {
            $cityIds = array_map('intval', (array) $input['cities']);
        } else {
            $cityIds = null;
        }

        if (isset($input['date'])) {
            $date = DateTime::createFromFormat('Y-m-d', $input['date']);

            if (!$date) {
                throw new InvalidArgumentException('Expected date format Y-m-d. Date: ' . $input['date']);
            }
        } else {
            $date = null;
        }

        if (isset($input['limit'])) {
            $limit = (int) $input['limit'];
        } else {
            $limit = null;
        }

        $orderBy = $input['orderBy'] ?? null;

        if (isset($input['direction'])) {
            $dir = strtoupper(trim((string)$input['direction']));
            $direction = SortDirectionEnum::tryFrom($dir);

            if (!$direction) {
                throw new InvalidArgumentException("Invalid sort direction: {$input['direction']}");
            }
        } else {
            $direction = null;
        }

        return new OrdersRequest(
            driverIds: $driverIds,
            cityIds: $cityIds,
            date: $date,
            limit: $limit,
            orderBy: $orderBy,
            direction: $direction
        );
    }
}
