<?php

namespace App\IO;

class ConsoleOutput implements OutputInterface
{
    /**
     * @param string $message
     * @return void
     */
    public function write(string $message): void
    {
        fwrite(STDOUT, $message);
    }

    /**
     * @param string $message
     * @return void
     */
    public function writeln(string $message): void
    {
        fwrite(STDOUT, $message . PHP_EOL);
    }
}
