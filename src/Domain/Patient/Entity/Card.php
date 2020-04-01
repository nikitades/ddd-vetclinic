<?php

namespace App\Domain\Patient\Entity;

use DateTime;
use App\Domain\Patient\Entity\Patient;
use App\Domain\Patient\Entity\MedicalCase;
use App\Domain\Patient\ValueObject\CardId;
use App\Domain\Patient\ValueObject\CardClosed;
use App\Domain\Patient\ValueObject\CardCreatedAt;
use App\Domain\Patient\ValueObject\MedicalCaseId;

class Card
{
    public function __construct()
    {
        $this->closed = new CardClosed(false);
        $this->createdAt = new CardCreatedAt(new DateTime());
    }

    protected CardId $id;

    public function getId(): CardId
    {
        return $this->id;
    }

    public function setId(CardId $cardId): void
    {
        $this->id = $cardId;
    }

    protected CardClosed $closed;

    public function getClosed(): CardClosed
    {
        return $this->closed;
    }

    protected Patient $patient;

    public function getPatient(): Patient
    {
        return $this->patient;
    }

    public function setPatient(Patient $patient): void
    {
        $this->patient = $patient;
    }

    /**
     * @var MedicalCase[]
     */
    protected array $cases = [];

    public function getCases(): array
    {
        return $this->cases;
    }

    public function addCase(MedicalCase $case): void
    {
        $this->cases[] = $case;
    }

    public function removeCase(MedicalCaseId $caseId): void
    {
        $this->cases = array_filter($this->cases, fn ($case) => !$case->getId()->equals($caseId));
    }

    protected CardCreatedAt $createdAt;

    public function getCreatedAt(): CardCreatedAt
    {
        return $this->createdAt;
    }
}
