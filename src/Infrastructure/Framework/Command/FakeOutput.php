<?php

namespace App\Infrastructure\Framework\Command;

use Symfony\Component\Console\Output\Output;

class FakeOutput extends Output
{
    protected function doWrite(string $message, bool $newline): void
    {
        //lalala
    }
}