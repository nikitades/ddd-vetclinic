<?php

namespace App\Infrastructure\Framework\CliResponse;

use App\Domain\Patient\Entity\Patient;
use Symfony\Component\Console\Style\SymfonyStyle;

final class GetPatientStateSuccessResponse extends AbstractCliResponse
{
    private Patient $patient;
    private bool $byId;

    public function __construct(SymfonyStyle $io, Patient $patient, bool $byId = true)
    {
        parent::__construct($io);
        $this->patient = $patient;
        $this->byId = $byId;
    }

    public function makeResponse(SymfonyStyle $io = null): void
    {
        if (empty($io)) $io = $this->io;
        $owner = $this->patient->getOwner();
        $belongsTo = $owner ? $owner->getName()->getValue() : "<none>";
        $io->comment("Found by: " . ($this->byId ? "ID" : "Name"));
        $io->title($this->patient->getName()->getValue());
        $io->table(
            ["Field", "Value"],
            [
                ["Name", $this->patient->getName()->getValue()],
                ["Species", $this->patient->getSpecies()->getValue()],
                ["Birth Date", $this->patient->getBirthDate()->getValue()->format("Y-m-d")],
                ["Belongs to", $belongsTo]
            ]
        );
    }
}
