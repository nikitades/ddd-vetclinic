<?php

namespace App\Test\Domain\Patient\Entity;

use PHPUnit\Framework\TestCase;
use App\Domain\Patient\Entity\Card;
use App\Domain\Patient\Entity\Owner;
use App\Domain\Patient\Entity\Patient;
use App\Domain\Patient\Entity\MedicalCase;
use App\Domain\Patient\ValueObject\CardCreatedAt;
use App\Domain\Patient\ValueObject\CardId;
use App\Domain\Patient\ValueObject\MedicalCaseId;
use App\Domain\Patient\ValueObject\OwnerId;
use App\Domain\Patient\ValueObject\OwnerName;
use App\Domain\Patient\ValueObject\PatientId;
use App\Domain\Patient\ValueObject\OwnerPhone;
use App\Domain\Patient\ValueObject\PatientName;
use App\Domain\Patient\ValueObject\OwnerAddress;
use App\Domain\Patient\ValueObject\PatientSpecies;
use App\Domain\Patient\ValueObject\PatientBirthDate;

class CardTest extends TestCase
{
    private function createOwner(): Owner
    {
        $o = new Owner(
            new OwnerName("Bubu"),
            new OwnerPhone("+79993334444"),
            new OwnerAddress("Haha benis again")
        );
        $o->setId(new OwnerId(35));
        return $o;
    }

    private function createPatient(Owner $owner, int $id = 36): Patient
    {
        $p = new Patient(
            new PatientName("A good boy"),
            PatientBirthDate::fromString("1960-03-05"),
            new PatientSpecies("Husky"),
        );
        $p->setId(new PatientId($id));
        $p->setOwner($owner);
        return $p;
    }

    private function createCase(Card $card, $id = 364): MedicalCase
    {
        $c = new MedicalCase();
        $c->setId(new MedicalCaseId($id));
        $c->setCard($card);
        return $c;
    }

    public function testNewCard()
    {
        $owner = $this->createOwner();
        $patient = $this->createPatient($owner, 77);
        $cardId = new CardId(66);
        $card = new Card();
        $card->setId($cardId);
        $card->setPatient($patient);
        static::assertEquals($cardId->getValue(), $card->getId()->getValue());
        static::assertNotNull($card->getPatient());
        static::assertEquals($card->getPatient(), $patient);
    }

    public function testAddCase()
    {
        $owner = $this->createOwner();
        $patient = $this->createPatient($owner, 741);

        $card = new Card(
            new CardId(35),
            $patient
        );

        static::assertIsArray($card->getCases());
        static::assertEmpty($card->getCases());
        $case = $this->createCase($card);
        $card->addCase($case);
        static::assertContainsOnly(MedicalCase::class, $card->getCases());
        static::assertCount(1, $card->getCases());
    }

    public function testRemoveCase()
    {
        $owner = $this->createOwner();
        $patient = $this->createPatient($owner);

        $card = new Card(
            new CardId(35),
            $patient
        );

        $case1 = $this->createCase($card, 345);
        $case2 = $this->createCase($card, 453);
        $case3 = $this->createCase($card, 596);

        $card->addCase($case1);
        $card->addCase($case2);
        $card->addCase($case3);

        static::assertContainsOnly(MedicalCase::class, $card->getCases());
        static::assertCount(3, $card->getCases());

        $card->removeCase($case2->getId());
        static::assertCount(2, $card->getCases());
        $newCases = array_values($card->getCases()); //to restore the numeration
        static::assertEquals($newCases[0]->getId(), $case1->getId());
        static::assertEquals($newCases[1]->getId(), $case3->getId());
    }

    public function testGetCases()
    {
        $owner = $this->createOwner();
        $patient = $this->createPatient($owner);

        $card = new Card(
            new CardId(35),
            $patient
        );

        $case1 = $this->createCase($card, 345);
        $case2 = $this->createCase($card, 453);
        $case3 = $this->createCase($card, 596);

        $card->addCase($case1);
        $card->addCase($case2);
        $card->addCase($case3);

        static::assertContainsOnly(MedicalCase::class, $card->getCases());
        static::assertCount(3, $card->getCases());
    }

    public function testGetPatient()
    {
        $owner = $this->createOwner();
        $patient = $this->createPatient($owner);
        $card = new Card();
        $card->setId(new CardId(666));
        $card->setPatient($patient);

        static::assertNotNull($card->getPatient());
        static::assertInstanceOf(Patient::class, $card->getPatient());
        static::assertGreaterThan(0, $card->getPatient()->getId()->getValue());
    }

    public function testGetCreatedAt()
    {

        $owner = $this->createOwner();
        $patient = $this->createPatient($owner);
        $card = new Card(
            new CardId(666),
            $patient
        );

        static::assertNotNull($card->getCreatedAt());
        static::assertInstanceOf(CardCreatedAt::class, $card->getCreatedAt());
        static::assertGreaterThan(0, $card->getCreatedAt()->getValue()->getTimestamp());
    }
}
