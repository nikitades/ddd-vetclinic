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
        $this->startedAt = new MedicalCaseStartedAt(new DateTime());
        $this->ended = new MedicalCaseEnded(false);
    }

    protected MedicalCaseId $id;

    public function getId(): MedicalCaseId
    {
        return $this->id;
    }

    public function setId(MedicalCaseId $id): void
    {
        $this->id = $id;
    }

    protected MedicalCaseDescription $description;

    public function getDescription(): MedicalCaseDescription
    {
        return $this->description;
    }

    public function setDescription(MedicalCaseDescription $value): void
    {
        $this->description = $value;
    }

    protected MedicalCaseTreatment $treatment;

    public function getTreatment(): MedicalCaseTreatment
    {
        return $this->treatment;
    }

    public function setTreatment(MedicalCaseTreatment $value): void
    {
        $this->treatment = $value;
    }

    protected Card $card;

    public function getCard(): Card
    {
        return $this->card;
    }

    public function setCard(Card $card): void
    {
        $this->card = $card;
    }

    protected MedicalCaseStartedAt $startedAt;

    public function getStartedAt(): MedicalCaseStartedAt
    {
        return $this->startedAt;
    }

    protected MedicalCaseEnded $ended;

    public function isEnded(): MedicalCaseEnded
    {
        return $this->ended;
    }

    public function end(): void
    {
        $this->ended = new MedicalCaseEnded(true);
    }

    protected ?MedicalCaseEndedAt $endedAt = null;

    public function getEndedAt(): ?MedicalCaseEndedAt
    {
        return $this->endedAt;
    }

    public function setEndedAt(MedicalCaseEndedAt $endedAt): void
    {
        $this->endedAt = $endedAt;
    }
}
