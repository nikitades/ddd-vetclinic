<?php

namespace App\Infrastructure\Framework\Command;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class InterceptableSymfonyStyle extends SymfonyStyle
{
    private InterceptableOutput $bufferedOutput;

    public function __construct()
    {
        parent::__construct(new ArgvInput(), new FakeOutput());
        $this->bufferedOutput = new InterceptableOutput();
    }

    /**
     * {@inheritdoc}
     */
    public function writeln($messages, int $type = self::OUTPUT_RAW)
    {
        if (!is_iterable($messages)) {
            $messages = [$messages];
        }

        foreach ($messages as $message) {
            $this->bufferedOutput->write($message, true, OutputInterface::OUTPUT_NORMAL);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function newLine(int $count = 1)
    {
        parent::newLine($count);
        $this->bufferedOutput->write(str_repeat("\n", $count));
    }

    /**
     * Returns the inner output object.
     *
     * @return InterceptableOutput
     */
    public function getOutput(): InterceptableOutput
    {
        return $this->bufferedOutput;
    }
}
