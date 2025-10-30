<?php

namespace App\Console\Commands;

readonly final class Commands
{
    /**
     * @return array<string>
     */
    public static function commands(): array
    {
        return [
            OrdersCommand::class,
            OrdersStatisticCommand::class,
            DriversCommand::class,
            CitiesCommand::class,
            CarsCommand::class,
        ];
    }
}
