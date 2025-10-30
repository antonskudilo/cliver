<?php

namespace App\IO;

interface OutputInterface
{
    /**
     * @param string $message
     * @return void
     */
    public function write(string $message): void;

    /**
     * @param string $message
     * @return void
     */
    public function writeln(string $message): void;
}
