<?php

namespace App\Test\Domain\Patient\Entity;

use PHPUnit\Framework\TestCase;
use App\Domain\Patient\Entity\Owner;
use App\Domain\Patient\Entity\Patient;
use App\Domain\Patient\ValueObject\OwnerId;
use App\Domain\Patient\ValueObject\OwnerName;
use App\Domain\Patient\ValueObject\PatientId;
use App\Domain\Patient\ValueObject\OwnerPhone;
use App\Domain\Patient\ValueObject\PatientName;
use App\Domain\Patient\ValueObject\OwnerAddress;
use App\Domain\Patient\ValueObject\OwnerRegisteredAt;
use App\Domain\Patient\ValueObject\PatientBirthDate;
use App\Domain\Patient\ValueObject\PatientSpecies;

class OwnerPatientsTest extends TestCase
{
    private function createOwner(): Owner
    {
        return new Owner(
            new OwnerId(35),
            new OwnerName("Bubu"),
            new OwnerPhone("+79993334444"),
            new OwnerAddress("Haha benis again")
        );
    }

    private function createPatient(Owner $owner, int $id = 36): Patient
    {
        return new Patient(
            new PatientId($id),
            new PatientName("A good boy"),
            PatientBirthDate::fromString("1960-03-05"),
            new PatientSpecies("Husky"),
            $owner
        );
    }

    public function testGetPatients()
    {
        $owner = $this->createOwner();
        static::assertEmpty($owner->getPatients());
        $patient1 = $this->createPatient($owner, 36);
        $patient2 = $this->createPatient($owner, 38);
        $patient3 = $this->createPatient($owner, 40);
        $owner->addPatient($patient1);
        $owner->addPatient($patient2);
        $owner->addPatient($patient3);
        static::assertCount(3, $owner->getPatients());
    }

    public function testAddPatient()
    {
        $owner = $this->createOwner();
        static::assertEmpty($owner->getPatients());
        $patient = $this->createPatient($owner);
        $owner->addPatient($patient);
        static::assertCount(1, $owner->getPatients());
    }

    public function testRemovePatient()
    {
        $owner = $this->createOwner();
        static::assertEmpty($owner->getPatients());
        $patient = $this->createPatient($owner);
        $owner->addPatient($patient);
        static::assertCount(1, $owner->getPatients());
        $owner->removePatient($patient->getId());
        static::assertEmpty($owner->getPatients());
    }

    public function testGetName()
    {
        $owner = $this->createOwner();
        static::assertNotNull($owner->getName());
        static::assertInstanceOf(OwnerName::class, $owner->getName());
    }

    public function testGetPhone()
    {
        $owner = $this->createOwner();
        static::assertNotNull($owner->getPhone());
        static::assertInstanceOf(OwnerPhone::class, $owner->getPhone());
    }

    public function testGetAddress()
    {
        $owner = $this->createOwner();
        static::assertNotNull($owner->getAddress());
        static::assertInstanceOf(OwnerAddress::class, $owner->getAddress());
    }

    public function testGetRegisteredAt()
    {
        $owner = $this->createOwner();
        static::assertNotNull($owner->getRegisteredAt());
        static::assertInstanceOf(OwnerRegisteredAt::class, $owner->getRegisteredAt());
    }
}
