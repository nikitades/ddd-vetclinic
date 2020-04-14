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
        $this->createdAt = CardCreatedAt::now();
    }

    protected ?CardId $id;
    protected CardClosed $closed;
    protected Patient $patient;
    /**
     * @var MedicalCase[]
     */
    protected array $cases = [];
    protected CardCreatedAt $createdAt;

    public function getId(): ?CardId
    {
        return $this->id;
    }

    public function setId(CardId $cardId): void
    {
        $this->id = $cardId;
    }

    public function getClosed(): CardClosed
    {
        /** @var MedicalCase $case */
        foreach ($this->cases as $case) {
            if (!$case->isEnded()->getValue()) return CardClosed::unclosed();
        }
        return CardClosed::closed();
    }

    public function getPatient(): Patient
    {
        return $this->patient;
    }

    public function setPatient(Patient $patient): void
    {
        $this->patient = $patient;
    }

    /**
     * Get cases contained in this card
     *
     * @return MedicalCase[]
     */
    public function getCases(): array
    {
        return $this->cases;
    }

    /**
     * Sets cases equal to the given list
     *
     * @param MedicalCase[] $cases
     * @return void
     */
    public function setCases(array $cases): void
    {
        $this->cases = $cases;
    }

    public function addCase(MedicalCase $case): void
    {
        $case->setCard($this);
        $this->cases[] = $case;
    }

    public function removeCase(MedicalCaseId $caseId): void
    {
        $this->cases = array_filter(
            $this->cases,
            fn ($case) => !$case->getId()->equals($caseId)
        );
    }

    public function getCreatedAt(): CardCreatedAt
    {
        return $this->createdAt;
    }

    public function setCreatedAt(CardCreatedAt $value): void
    {
        $this->createdAt = $value;
    }
}
