<?php

namespace App\Test\Domain\Patient\Entity;

use PHPUnit\Framework\TestCase;
use App\Domain\Patient\Entity\Card;
use App\Domain\Patient\Entity\Owner;
use App\Domain\Patient\Entity\Patient;
use App\Domain\Patient\Entity\MedicalCase;
use App\Domain\Patient\ValueObject\CardId;
use App\Domain\Patient\ValueObject\MedicalCaseDescription;
use App\Domain\Patient\ValueObject\MedicalCaseId;
use App\Domain\Patient\ValueObject\MedicalCaseTreatment;
use App\Domain\Patient\ValueObject\OwnerId;
use App\Domain\Patient\ValueObject\OwnerName;
use App\Domain\Patient\ValueObject\PatientId;
use App\Domain\Patient\ValueObject\OwnerPhone;
use App\Domain\Patient\ValueObject\PatientName;
use App\Domain\Patient\ValueObject\OwnerAddress;
use App\Domain\Patient\ValueObject\PatientSpecies;
use App\Domain\Patient\ValueObject\PatientBirthDate;

class PatientTest extends TestCase
{
    private function createOwner(): Owner
    {
        $o = new Owner(
            new OwnerName("Haha benis"),
            new OwnerPhone("+73332224444"),
            new OwnerAddress("ул. Пушкина, дом Колотушкина")
        );
        $o->setId(new OwnerId(555));
        return $o;
    }

    /**
     * Quickly creates a patien in various ways
     *
     * @param bool[] $params
     * @return Patient
     */
    private function createPatient(array $params = []): Patient
    {
        $p = new Patient(
            new PatientName("Jogn"),
            PatientBirthDate::fromString("2000-01-01"),
            new PatientSpecies("Crocodile")
        );

        $p->setId(new PatientId(33));
        $p->setOwner($this->createOwner());

        if (!empty($params['with_card'])) {
            $card = new Card();
            $card->setId(new CardId(73));
            $p->addCard($card);
        }

        return $p;
    }

    public function testAddCard(): void
    {
        $patient = $this->createPatient();
        $card = new Card();
        static::assertCount(0, $patient->getCards());
        $patient->addCard($card);
        static::assertCount(1, $patient->getCards());
        static::assertContainsOnly(Card::class, $patient->getCards());
    }

    /**
     * @expectedException App\Domain\Patient\Exception\MoreThanOneActiveCardIsNotAllowedException
     */
    public function testMoreThan1CardIsNotAvailable(): void
    {
        $patient = $this->createPatient();
        $card = new Card();
        static::assertCount(0, $patient->getCards());
        $patient->addCard($card);
        $patient->addCard($card);
    }

    public function testRemoveCard(): void
    {
        $patientParams = [
            'with_card' => true
        ];
        $patient = $this->createPatient($patientParams);
        $cards = $patient->getCards();
        static::assertCount(1, $cards);
        $card = $cards[0];
        $patient->removeCard($card->getId());
        static::assertCount(0, $patient->getCards());
    }

    public function testGetCards(): void
    {
        $patient = $this->createPatient();
        $card = new Card();
        static::assertEmpty($patient->getCards());
        $patient->addCard($card);
        static::assertCount(1, $patient->getCards());
        static::assertContainsOnly(Card::class, $patient->getCards());
    }

    public function testGetName(): void
    {
        $patient = $this->createPatient();
        static::assertNotNull($patient->getName());
        static::assertInstanceOf(PatientName::class, $patient->getName());
    }

    public function testGetBirthDate(): void
    {
        $patient = $this->createPatient();
        static::assertNotNull($patient->getBirthDate());
        static::assertInstanceOf(PatientBirthDate::class, $patient->getBirthDate());
    }

    public function testGetSpecies(): void
    {
        $patient = $this->createPatient();
        static::assertNotNull($patient->getSpecies());
        static::assertInstanceOf(PatientSpecies::class, $patient->getSpecies());
    }

    public function testGetOwner(): void
    {
        $patient = $this->createPatient();
        static::assertNotNull($patient->getOwner());
        static::assertInstanceOf(Owner::class, $patient->getOwner());
    }

    public function testRelease(): void
    {
        $patientParams = [
            'with_card' => true
        ];
        $patient = $this->createPatient($patientParams);
        $card = $patient->getCurrentCard();
        static::assertNotNull($card);
        if (empty($card)) return;
        /** @var MedicalCase[] */
        $cases = $card->getCases();
        static::assertEmpty($cases);
        
        $case = new MedicalCase();
        $case->setDescription(new MedicalCaseDescription("Some description"));
        $case->setTreatment(new MedicalCaseTreatment("Some treatment"));
        $case->setId(new MedicalCaseId(32));
        
        $card = $patient->getCurrentCard();
        static::assertNotNull($card);
        if (empty($card)) return;
        $card->addCase($case);
        /** @var MedicalCase[] */
        $cases = $card->getCases();
        static::assertCount(1, $cases);
        static::assertContainsOnly(MedicalCase::class, $cases);
        static::assertFalse($cases[0]->isEnded()->getValue());

        $patient->release();
        static::assertTrue($cases[0]->isEnded()->getValue());
    }
}
