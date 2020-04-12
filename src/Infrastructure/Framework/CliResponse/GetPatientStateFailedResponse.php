<?php

namespace App\Infrastructure\Framework\CliResponse;

use Symfony\Component\Console\Style\SymfonyStyle;

class GetPatientStateFailedResponse extends AbstractCliResponse
{
    /** @var mixed */
    private $id;

    /**
     * @param SymfonyStyle $style
     * @param mixed $id
     */
    public function __construct(SymfonyStyle $style, $id)
    {
        parent::__construct($style);
    }

    public function makeResponse(SymfonyStyle $io = null): void
    {
        if (empty($io)) $io = $this->io;
        $io->note("Tried to search with the key " . $this->id);
        $io->error("Patient was not found!");
    }
}