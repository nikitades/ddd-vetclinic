<?php

namespace App\Domain\Patient\Entity;

use App\Domain\Patient\ValueObject\CardId;
use App\Domain\Patient\ValueObject\PatientId;
use App\Domain\Patient\ValueObject\PatientName;
use App\Domain\Patient\ValueObject\PatientSpecies;
use App\Domain\Patient\ValueObject\PatientBirthDate;

class Patient
{
    public function __construct(PatientId $id, PatientName $name, PatientBirthDate $birthDate, PatientSpecies $species, Owner $owner)
    {
        $this->id = $id;
        $this->name = $name;
        $this->birthDate = $birthDate;
        $this->species = $species;
        $this->owner = $owner;
    }

    protected PatientId $id;

    public function getId(): PatientId
    {
        return $this->id;
    }

    protected PatientName $name;

    public function getName(): PatientName
    {
        return $this->name;
    }

    protected PatientBirthDate $birthDate;

    public function getBirthDate(): PatientBirthDate
    {
        return $this->birthDate;
    }

    protected PatientSpecies $species;

    public function getSpecies(): PatientSpecies
    {
        return $this->species;
    }

    /**
     * @var Card[]
     */
    protected array $cards = [];

    public function getCards(): array
    {
        return $this->cards;
    }

    public function addCard(Card $card): void
    {
        $this->cards[] = $card;
    }

    public function removeCard(CardId $cardId): void
    {
        $this->cards = array_filter($this->cards, fn ($card) => $card->getId() !== $cardId);
    }

    protected Owner $owner;

    public function getOwner(): Owner
    {
        return $this->owner;
    }

    public function isCured(): bool
    {
        $cards = $this->getCards();
        $cases = array_map(fn ($card) => $card->getCases(), $cards);
        foreach ($cases as $case) if (!$case->isEnded()) return false;
        return true;
    }
}
