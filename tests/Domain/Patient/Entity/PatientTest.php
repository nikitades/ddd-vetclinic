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

class PatientCardTest extends TestCase
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

    public function testAddCard()
    {
        $patient = $this->createPatient();
        $card = new Card(
            new CardId(35),
            $patient
        );
        static::assertCount(0, $patient->getCards());
        $patient->addCard($card);
        static::assertCount(1, $patient->getCards());
        static::assertContainsOnly(Card::class, $patient->getCards());
    }

    /**
     * @expectedException App\Domain\Patient\Exception\MoreThanOneActiveCardIsNotAllowedException
     */
    public function testMoreThan1CardIsNotAvailable()
    {
        $patient = $this->createPatient();
        $card = new Card(
            new CardId(35),
            $patient
        );
        static::assertCount(0, $patient->getCards());
        $patient->addCard($card);
        $patient->addCard($card);
    }

    public function testRemoveCard()
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

    public function testRelease()
    {
        $patientParams = [
            'with_card' => true
        ];
        $patient = $this->createPatient($patientParams);
        /** @var MedicalCase[] */
        $cases = $patient->getCurrentCard()->getCases();
        static::assertEmpty($cases);
        
        $case = new MedicalCase();
        $case->setDescription(new MedicalCaseDescription("Some description"));
        $case->setTreatment(new MedicalCaseTreatment("Some treatment"));
        $case->setId(new MedicalCaseId(32));
        
        $patient->getCurrentCard()->addCase($case);
        /** @var MedicalCase[] */
        $cases = $patient->getCurrentCard()->getCases();
        static::assertCount(1, $cases);
        static::assertContainsOnly(MedicalCase::class, $cases);
        static::assertFalse($cases[0]->isEnded()->getValue());

        $patient->release();
        static::assertTrue($cases[0]->isEnded()->getValue());
    }
}
