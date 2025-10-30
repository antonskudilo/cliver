<?php

namespace App\IO;

class BufferedOutput implements OutputInterface
{
    /**
     * @var string
     */
    private string $buffer = '';

    /**
     * @param string $message
     * @return void
     */
    public function write(string $message): void
    {
        $this->buffer .= $message;
    }

    /**
     * @param string $message
     * @return void
     */
    public function writeln(string $message): void
    {
        $this->buffer .= $message . PHP_EOL;
    }

    /**
     * @return string
     */
    public function getBuffer(): string
    {
        return $this->buffer;
    }

    /**
     * @return void
     */
    public function clear(): void
    {
        $this->buffer = '';
    }
}
