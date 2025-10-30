<?php

namespace App\Renders;

use App\IO\ConsoleOutput;
use App\IO\OutputInterface;
use App\Providers\AppResolver;
use Throwable;

abstract class AbstractRender
{
    /**
     * @var OutputInterface|ConsoleOutput|mixed
     */
    protected OutputInterface $output;

    /**
     * @throws Throwable
     */
    public function __construct(
        protected readonly AppResolver $resolver
    ) {
        $this->output = $this->resolver->make(OutputInterface::class, ConsoleOutput::class);
    }

    /**
     * @param string $message
     * @return void
     */
    protected function println(string $message = ''): void
    {
        $this->output->writeln($message);
    }

    /**
     * @param string $label
     * @param string $value
     * @param int $padLength
     * @return string
     */
    protected function pad(string $label, string $value, int $padLength = 25): string
    {
        return str_pad($label, $padLength) . $value;
    }

    /**
     * @param array<string,string|int|float> $rows
     */
    protected function padAuto(array $rows): void
    {
        if (empty($rows)) {
            return;
        }

        $maxLen = max(array_map('strlen', array_keys($rows))) + 2;

        foreach ($rows as $label => $value) {
            $this->println($this->pad($label, (string)$value, $maxLen));
        }
    }

    /**
     * @param string $title
     * @return void
     */
    protected function renderHeader(string $title): void
    {
        $this->println("=== {$title} ===");
    }

    /**
     * @return void
     */
    protected function renderSeparator(): void
    {
        $this->println(str_repeat('=', 20));
    }
}
