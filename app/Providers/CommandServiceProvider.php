<?php

namespace App\Providers;

use App\Console\Commands\Commands;
use Cliver\Core\Providers\BaseCommandServiceProvider;

class CommandServiceProvider extends BaseCommandServiceProvider
{
    /**
     * @return array<string>
     */
    protected function commands(): array
    {
        return Commands::commands();
    }
}
