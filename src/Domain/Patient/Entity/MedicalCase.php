<?php

namespace App\Domain\Patient\Entity;

use DateTime;
use App\Domain\Patient\Entity\Card;
use App\Domain\Patient\ValueObject\MedicalCaseId;
use App\Domain\Patient\ValueObject\MedicalCaseEnded;
use App\Domain\Patient\ValueObject\MedicalCaseEndedAt;
use App\Domain\Patient\ValueObject\MedicalCaseStartedAt;
use App\Domain\Patient\ValueObject\MedicalCaseTreatment;
use App\Domain\Patient\ValueObject\MedicalCaseDescription;

class MedicalCase
{

    public function __construct()
    {
        $this->startedAt = MedicalCaseStartedAt::now();
        $this->ended = new MedicalCaseEnded(false);
    }

    protected MedicalCaseId $id;
    protected MedicalCaseDescription $description;
    protected MedicalCaseTreatment $treatment;
    protected Card $card;
    protected MedicalCaseStartedAt $startedAt;
    protected MedicalCaseEnded $ended;
    protected ?MedicalCaseEndedAt $endedAt = null;

    public function getId(): MedicalCaseId
    {
        return $this->id;
    }

    public function setId(MedicalCaseId $id): void
    {
        $this->id = $id;
    }

    public function getDescription(): MedicalCaseDescription
    {
        return $this->description;
    }

    public function setDescription(MedicalCaseDescription $value): void
    {
        $this->description = $value;
    }

    public function getTreatment(): MedicalCaseTreatment
    {
        return $this->treatment;
    }

    public function setTreatment(MedicalCaseTreatment $value): void
    {
        $this->treatment = $value;
    }

    public function getCard(): Card
    {
        return $this->card;
    }

    public function setCard(Card $card): void
    {
        $this->card = $card;
    }

    public function getStartedAt(): MedicalCaseStartedAt
    {
        return $this->startedAt;
    }

    public function setStartedAt(MedicalCaseStartedAt $value): void
    {
        $this->startedAt = $value;
    }

    public function isEnded(): MedicalCaseEnded
    {
        return $this->ended;
    }

    public function end(): void
    {
        $this->ended = new MedicalCaseEnded(true);
    }

    public function getEndedAt(): ?MedicalCaseEndedAt
    {
        return $this->endedAt;
    }

    public function setEndedAt(MedicalCaseEndedAt $endedAt): void
    {
        $this->endedAt = $endedAt;
    }
}
