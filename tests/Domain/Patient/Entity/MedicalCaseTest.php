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
        return new Owner(
            new OwnerId(889),
            new OwnerName("Uncle Sam"),
            new OwnerPhone("+79996767676"),
            new OwnerAddress("NYC, 5th av.")
        );
    }

    private function createPatient(): Patient
    {
        return new Patient(
            new PatientId(32),
            new PatientName("Samson"),
            new PatientBirthDate((new DateTime())->sub(new DateInterval("P2Y"))),
            new PatientSpecies("Lion"),
            $this->createOwner()
        );
    }

    private function createCard(): Card
    {
        return new Card(
            new CardId(33),
            $this->createPatient()
        );
    }

    private function createCase(): MedicalCase
    {
        return new MedicalCase(
            new MedicalCaseId(35),
            $this->createCard()
        );
    }

    public function testDescription()
    {
        $case = $this->createCase();
        static::assertInstanceOf(MedicalCase::class, $case);
        $desc = "It looks like your lion is just a mug of a shale oil";
        $mcd = new MedicalCaseDescription($desc);
        static::assertInstanceOf(MedicalCaseDescription::class, $mcd);
        $case->setDescription($mcd);
        static::assertEquals($mcd, $case->getDescription());
    }

    public function testTreatment()
    {
        $case = $this->createCase();
        static::assertInstanceOf(MedicalCase::class, $case);
        $treatment = "Just create boston dynamics robots";
        $mct = new MedicalCaseTreatment($treatment);
        static::assertInstanceOf(MedicalCaseTreatment::class, $mct);
        $case->setTreatment($mct);
        static::assertEquals($mct, $case->getTreatment());
    }

    public function testGetCard()
    {
        $case = $this->createCase();
        static::assertNotNull($case->getCard());
        static::assertInstanceOf(MedicalCase::class, $case);
        static::assertInstanceOf(Card::class, $case->getCard());
        static::assertInstanceOf(Patient::class, $case->getCard()->getPatient());
        static::assertInstanceOf(Owner::class, $case->getCard()->getPatient()->getOwner());
        static::assertNotEmpty($case->getCard()->getPatient()->getOwner()->getName());
    }

    public function testGetStartedAt()
    {
        $case = $this->createCase();
        static::assertNotNull($case->getStartedAt());
        static::assertInstanceOf(MedicalCaseStartedAt::class, $case->getStartedAt());
    }

    public function testGetEndedAt()
    {
        $case = $this->createCase();
        static::assertNull($case->getEndedAt());
        $case->setEndedAt(MedicalCaseEndedAt::now());
        static::assertNotNull($case->getEndedAt());
        static::assertInstanceOf(MedicalCaseEndedAt::class, $case->getEndedAt());
    }

    public function testIsEnded()
    {
        $case = $this->createCase();
        static::assertEquals(false, $case->isEnded()->getValue());
        $case->end();
        static::assertEquals(true, $case->isEnded()->getValue());
    }
}
