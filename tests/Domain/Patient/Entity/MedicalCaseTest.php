<?php

namespace App\Test\Domain\Patient\Entity;

use DateTime;
use DateInterval;
use PHPUnit\Framework\TestCase;
use App\Domain\Patient\Entity\Card;
use App\Domain\Patient\Entity\Owner;
use App\Domain\Patient\Entity\Patient;
use App\Domain\Patient\Entity\MedicalCase;
use App\Domain\Patient\ValueObject\CardId;
use App\Domain\Patient\ValueObject\OwnerId;
use App\Domain\Patient\ValueObject\OwnerName;
use App\Domain\Patient\ValueObject\PatientId;
use App\Domain\Patient\ValueObject\OwnerEmail;
use App\Domain\Patient\ValueObject\OwnerPhone;
use App\Domain\Patient\ValueObject\PatientName;
use App\Domain\Patient\ValueObject\OwnerAddress;
use App\Domain\Patient\ValueObject\MedicalCaseId;
use App\Domain\Patient\ValueObject\PatientSpecies;
use App\Domain\Patient\ValueObject\PatientBirthDate;
use App\Domain\Patient\ValueObject\MedicalCaseEndedAt;
use App\Domain\Patient\ValueObject\MedicalCaseStartedAt;
use App\Domain\Patient\ValueObject\MedicalCaseTreatment;
use App\Domain\Patient\ValueObject\MedicalCaseDescription;

class MedicalCaseTest extends TestCase
{
    private function createOwner(): Owner
    {
        $o = new Owner(
            new OwnerName("Uncle Sam"),
            new OwnerPhone("+79996767676"),
            new OwnerAddress("NYC, 5th av."),
            new OwnerEmail("sam@uncle.com")
        );
        $o->setId(new OwnerId(889));
        return $o;
    }

    private function createPatient(): Patient
    {
        $p = new Patient(
            new PatientName("Samson"),
            new PatientBirthDate((new DateTime())->sub(new DateInterval("P2Y"))),
            new PatientSpecies("Lion")
        );
        $p->setId(new PatientId(32));
        $p->setOwner($this->createOwner());
        return $p;
    }

    private function createCard(): Card
    {
        $c = new Card();
        $c->setId(new CardId(33));
        $c->setPatient($this->createPatient());
        return $c;
    }

    private function createCase(): MedicalCase
    {
        $c = new MedicalCase();
        $c->setId(new MedicalCaseId(35));
        $c->setCard($this->createCard());
        return $c;
    }

    public function testDescription(): void
    {
        $case = $this->createCase();
        static::assertInstanceOf(MedicalCase::class, $case);
        $desc = "It looks like your lion is just a mug of a shale oil";
        $mcd = new MedicalCaseDescription($desc);
        static::assertInstanceOf(MedicalCaseDescription::class, $mcd);
        $case->setDescription($mcd);
        static::assertEquals($mcd, $case->getDescription());
    }

    public function testTreatment(): void
    {
        $case = $this->createCase();
        static::assertInstanceOf(MedicalCase::class, $case);
        $treatment = "Just create boston dynamics robots";
        $mct = new MedicalCaseTreatment($treatment);
        static::assertInstanceOf(MedicalCaseTreatment::class, $mct);
        $case->setTreatment($mct);
        static::assertEquals($mct, $case->getTreatment());
    }

    public function testGetCard(): void
    {
        $case = $this->createCase();
        static::assertNotNull($case->getCard());
        static::assertInstanceOf(MedicalCase::class, $case);
        static::assertInstanceOf(Card::class, $case->getCard());
        static::assertInstanceOf(Patient::class, $case->getCard()->getPatient());
        static::assertInstanceOf(Owner::class, $case->getCard()->getPatient()->getOwner());
        $owner = $case->getCard()->getPatient()->getOwner();
        static::assertNotNull($owner);
        if (is_null($owner)) return;
        static::assertNotEmpty($owner->getName());
    }

    public function testGetStartedAt(): void
    {
        $case = $this->createCase();
        static::assertNotNull($case->getStartedAt());
        static::assertInstanceOf(MedicalCaseStartedAt::class, $case->getStartedAt());
    }

    public function testGetEndedAt(): void
    {
        $case = $this->createCase();
        static::assertNull($case->getEndedAt());
        $case->setEndedAt(MedicalCaseEndedAt::now());
        static::assertNotNull($case->getEndedAt());
        static::assertInstanceOf(MedicalCaseEndedAt::class, $case->getEndedAt());
    }

    public function testIsEnded(): void
    {
        $case = $this->createCase();
        static::assertEquals(false, $case->isEnded()->getValue());
        $case->end();
        static::assertEquals(true, $case->isEnded()->getValue());
    }
}
