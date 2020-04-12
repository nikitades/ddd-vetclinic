<?php

namespace App\Infrastructure\Framework\CliResponse;

use Symfony\Component\Console\Style\SymfonyStyle;
use App\Domain\Shared\Exception\WrongValueException;
use App\Infrastructure\Framework\Command\InterceptableSymfonyStyle;
use App\Infrastructure\Framework\Command\InterceptedOutput;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;

abstract class AbstractCliResponse
{
    protected SymfonyStyle $io;

    public function __construct(SymfonyStyle $io)
    {
        $this->io = $io;
    }

    public function makeResponse(SymfonyStyle $io): void
    {
        throw new WrongValueException("You must implement the command output");
    }

    public function getResponse(): string
    {
        $io = new InterceptableSymfonyStyle();
        $this->makeResponse($io);
        $result = $io->getOutput()->getContentAndFlush();
        if (empty($result)) throw new WrongValueException("Command returned empty response");
        return $result;
    }
}
