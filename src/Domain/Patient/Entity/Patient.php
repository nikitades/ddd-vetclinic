<?php

namespace App\Domain\Patient\Entity;

use App\Domain\Patient\Entity\Card;
use App\Domain\Patient\ValueObject\CardId;
use App\Domain\Patient\ValueObject\PatientId;
use App\Domain\Patient\ValueObject\PatientName;
use App\Domain\Patient\ValueObject\PatientSpecies;
use App\Domain\Patient\ValueObject\PatientBirthDate;
use App\Domain\Patient\Exception\MoreThanOneActiveCardIsNotAllowedException;
use App\Domain\Patient\Exception\NoPatientCardsFoundException;
use Exception;

class Patient
{
    public function __construct(PatientName $name, PatientBirthDate $birthDate, PatientSpecies $species)
    {
        $this->name = $name;
        $this->birthDate = $birthDate;
        $this->species = $species;
    }

    protected ?PatientId $id = null;
    protected PatientName $name;
    protected PatientBirthDate $birthDate;
    protected PatientSpecies $species;
    protected ?Owner $owner = null;
    /**
     * @var Card[]
     */
    protected array $cards = [];

    public function getId(): ?PatientId
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

    /**
     * Get patients' cards
     *
     * @return Card[]
     */
    public function getCards(): array
    {
        return $this->cards;
    }

    /**
     * Sets cards equal to the given list
     *
     * @param Card[] $cards
     * @return void
     */
    public function setCards(array $cards): void
    {
        $this->cards = $cards;
    }

    public function getCurrentCard(): ?Card
    {
        if (empty($this->cards)) return null;

        $notEmptyCards = array_filter($this->cards, fn ($card) => !$card->getClosed()->getValue());
        if (!empty($notEmptyCards)) return $notEmptyCards[0];

        /** @var Card */
        $latestCard = $this->cards[0];

        foreach ($this->cards as $card) {
            if ($card->getCreatedAt()->getValue() > $latestCard->getCreatedAt()->getValue()) $latestCard = $card;
        }

        return $latestCard;
    }

    public function addCard(Card $card): void
    {
        try {
            $currentCard = $this->getCurrentCard();
        } catch (NoPatientCardsFoundException $e) {
            $currentCard = null;
        } catch (Exception $e) {
            throw $e;
        }
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

    public function getOwner(): ?Owner
    {
        return $this->owner;
    }

    public function setOwner(Owner $owner): void
    {
        $owner->addPatient($this);
        $this->owner = $owner;
    }

    public function isCured(): bool
    {
        /**
         * @var Card $card
         */
        foreach ($this->getCards() as $card) {
            foreach ($card->getCases() as $case) {
                if (!$case->isEnded()->getValue()) return false;
            }
        }
        return true;
    }

    public function release(): void
    {
        /**
         * @var Card $card
         */
        foreach ($this->getCards() as $card) {
            foreach ($card->getCases() as $case) {
                $case->end();
            }
        }
    }
}
