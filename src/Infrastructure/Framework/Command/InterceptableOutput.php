<?php

namespace App\Infrastructure\Framework\Command;

use Symfony\Component\Console\Output\BufferedOutput;

class InterceptableOutput extends BufferedOutput
{

    private string $messages = "";

    /**
     *
     * @param mixed $messages
     * @param boolean $newline
     * @param integer $options
     * @return void
     */
    public function write($messages, bool $newline = false, int $options = self::OUTPUT_NORMAL)
    {
        parent::write($messages, $newline, $options);
    }

    protected function doWrite(string $message, bool $newline): void
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
