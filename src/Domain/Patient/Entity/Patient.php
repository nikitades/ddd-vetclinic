<?php

namespace App\Domain\Patient\Entity;

use App\Domain\Patient\Exception\MoreThanOneActiveCardIsNotAllowedException;
use App\Domain\Patient\ValueObject\CardId;
use App\Domain\Patient\ValueObject\PatientId;
use App\Domain\Patient\ValueObject\PatientName;
use App\Domain\Patient\ValueObject\PatientSpecies;
use App\Domain\Patient\ValueObject\PatientBirthDate;

class Patient
{
    public function __construct(PatientName $name, PatientBirthDate $birthDate, PatientSpecies $species)
    {
        $this->name = $name;
        $this->birthDate = $birthDate;
        $this->species = $species;
    }

    protected PatientId $id;

    public function getId(): PatientId
    {
        return $this->id;
    }

    public function setId(PatientId $id): void
    {
        $this->id = $id;
    }

    protected PatientName $name;

    public function getName(): PatientName
    {
        return $this->name;
    }

    public function setName(PatientName $name): void
    {
        $this->name = $name;
    }

    protected PatientBirthDate $birthDate;

    public function getBirthDate(): PatientBirthDate
    {
        return $this->birthDate;
    }

    public function setBirthDate(PatientBirthDate $patientBirthDate): void
    {
        $this->birthDate = $patientBirthDate;
    }

    protected PatientSpecies $species;

    public function getSpecies(): PatientSpecies
    {
        return $this->species;
    }

    public function setSpecies(PatientSpecies $species): void
    {
        $this->species = $species;
    }

    /**
     * @var Card[]
     */
    protected array $cards = [];

    public function getCards(): array
    {
        return $this->cards;
    }

    public function getCurrentCard(): ?Card
    {
        $cards = array_filter($this->cards, fn ($card) => !$card->getClosed()->getValue());
        return $cards[0] ?? null;
    }

    public function addCard(Card $card): void
    {
        $currentCard = $this->getCurrentCard();
        if ($currentCard) {
            throw new MoreThanOneActiveCardIsNotAllowedException($this->name->getValue());
        }
        $card->setPatient($this);
        $this->cards[] = $card;
    }

    public function removeCard(CardId $cardId): void
    {
        $this->cards = array_filter($this->cards, fn ($card) => !$card->getId()->equals($cardId));
    }

    protected Owner $owner;

    public function getOwner(): Owner
    {
        return $this->owner;
    }

    public function setOwner(Owner $owner): void
    {
        $this->owner = $owner;
    }

    public function isCured(): bool
    {
        /**
         * @var Card
         */
        foreach ($this->getCards() as $card) {
            foreach ($card->getCases() as $case) {
                if (!$case->isEnded()) return false;
            }
        }
        return true;
    }

    public function release(): void
    {
        /**
         * @var Card
         */
        foreach ($this->getCards() as $card) {
            foreach ($card->getCases() as $case) {
                $case->end();
            }
        }
    }
}
