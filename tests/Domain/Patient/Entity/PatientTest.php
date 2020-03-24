<?php

namespace App\Test\Domain\Patient\Entity;

use PHPUnit\Framework\TestCase;
use App\Domain\Patient\Entity\Card;
use App\Domain\Patient\Entity\Owner;
use App\Domain\Patient\Entity\Patient;
use App\Domain\Patient\ValueObject\CardId;
use App\Domain\Patient\ValueObject\OwnerId;
use App\Domain\Patient\ValueObject\OwnerName;
use App\Domain\Patient\ValueObject\PatientId;
use App\Domain\Patient\ValueObject\OwnerPhone;
use App\Domain\Patient\ValueObject\PatientName;
use App\Domain\Patient\ValueObject\OwnerAddress;
use App\Domain\Patient\ValueObject\PatientSpecies;
use App\Domain\Patient\ValueObject\PatientBirthDate;

class PatientCardTest extends TestCase
{
    private function createOwner(): Owner
    {
        return new Owner(
            new OwnerId(555),
            new OwnerName("Haha benis"),
            new OwnerPhone("+73332224444"),
            new OwnerAddress("ул. Пушкина, дом Колотушкина")
        );
    }

    private function createPatient(): Patient
    {
        return new Patient(
            new PatientId(33),
            new PatientName("Jogn"),
            PatientBirthDate::fromString("2000-01-01"),
            new PatientSpecies("Crocodile"),
            $this->createOwner()
        );
    }

    public function testAddCard()
    {
        $patient = $this->createPatient();
        $card = new Card(
            new CardId(35),
            $patient
        );
        static::assertEmpty($patient->getCards());
        $patient->addCard($card);
        static::assertCount(1, $patient->getCards());
        static::assertContainsOnly(Card::class, $patient->getCards());
    }

    public function testRemoveCard()
    {
        $patient = $this->createPatient();
        $card = new Card(
            new CardId(35),
            $patient
        );
        static::assertEmpty($patient->getCards());
        $patient->addCard($card);
        static::assertCount(1, $patient->getCards());
        $patient->removeCard($card->getId());
        static::assertCount(0, $patient->getCards());
    }

    public function testGetCards()
    {
        $patient = $this->createPatient();
        $card = new Card(
            new CardId(35),
            $patient
        );
        static::assertEmpty($patient->getCards());
        $patient->addCard($card);
        static::assertCount(1, $patient->getCards());
        static::assertContainsOnly(Card::class, $patient->getCards());
    }

    public function testGetName()
    {
        $patient = $this->createPatient();
        static::assertNotNull($patient->getName());
        static::assertInstanceOf(PatientName::class, $patient->getName());
    }

    public function testGetBirthDate()
    {
        $patient = $this->createPatient();
        static::assertNotNull($patient->getBirthDate());
        static::assertInstanceOf(PatientBirthDate::class, $patient->getBirthDate());
    }

    public function testGetSpecies()
    {
        $patient = $this->createPatient();
        static::assertNotNull($patient->getSpecies());
        static::assertInstanceOf(PatientSpecies::class, $patient->getSpecies());
    }

    public function testGetOwner()
    {
        $patient = $this->createPatient();
        static::assertNotNull($patient->getOwner());
        static::assertInstanceOf(Owner::class, $patient->getOwner());
    }
}
