<?php

namespace App\Domain\Patient\Entity;

use App\Framework\Entity\Patient as DBALPatient;
use App\Domain\Patient\ValueObject\CardId;
use App\Domain\Shared\Entity\IDomainEntity;
use App\Domain\Patient\ValueObject\PatientId;
use App\Domain\Patient\ValueObject\PatientName;
use App\Domain\Patient\ValueObject\PatientSpecies;
use App\Domain\Patient\ValueObject\PatientBirthDate;
use App\Domain\Patient\Exception\MoreThanOneActiveCardIsNotAllowedException;

class Patient
{
    public function __construct(PatientName $name, PatientBirthDate $birthDate, PatientSpecies $species)
    {
        $this->name = $name;
        $this->birthDate = $birthDate;
        $this->species = $species;
    }

    protected PatientId $id;
    protected PatientName $name;
    protected PatientBirthDate $birthDate;
    protected PatientSpecies $species;
    protected Owner $owner;
    /**
     * @var Card[]
     */
    protected array $cards = [];

    public function getId(): PatientId
    {
        return $this->id;
    }

    public function setId(PatientId $id): void
    {
        $this->id = $id;
    }

    public function getName(): PatientName
    {
        return $this->name;
    }

    public function setName(PatientName $name): void
    {
        $this->name = $name;
    }

    public function getBirthDate(): PatientBirthDate
    {
        return $this->birthDate;
    }

    public function setBirthDate(PatientBirthDate $patientBirthDate): void
    {
        $this->birthDate = $patientBirthDate;
    }

    public function getSpecies(): PatientSpecies
    {
        return $this->species;
    }

    public function setSpecies(PatientSpecies $species): void
    {
        $this->species = $species;
    }

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
