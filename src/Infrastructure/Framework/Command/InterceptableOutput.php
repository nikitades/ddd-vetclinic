<?php

namespace App\Infrastructure\Framework\Command;

use Symfony\Component\Console\Output\BufferedOutput;

class InterceptableOutput extends BufferedOutput
{

    private string $messages = "";

    public function write($messages, bool $newline = false, int $options = self::OUTPUT_NORMAL)
    {
        parent::write($messages, $newline, $options);
    }

    protected function doWrite(string $message, bool $newline)
    {
        $this->messages .= ($message . ($newline ? "\n" : ""));
    }

    public function getContentAndFlush(): string
    {
        $output = $this->messages;
        $this->messages = "";
        return $output;
    }
}
