<?php

namespace App\Test\Domain\Patient\Entity;

use PHPUnit\Framework\TestCase;
use App\Domain\Patient\Entity\Owner;
use App\Domain\Patient\Entity\Patient;
use App\Domain\Patient\ValueObject\OwnerId;
use App\Domain\Patient\ValueObject\OwnerName;
use App\Domain\Patient\ValueObject\PatientId;
use App\Domain\Patient\ValueObject\OwnerEmail;
use App\Domain\Patient\ValueObject\OwnerPhone;
use App\Domain\Patient\ValueObject\PatientName;
use App\Domain\Patient\ValueObject\OwnerAddress;
use App\Domain\Patient\ValueObject\PatientSpecies;
use App\Domain\Patient\ValueObject\PatientBirthDate;
use App\Domain\Patient\ValueObject\OwnerRegisteredAt;

class OwnerTest extends TestCase
{
    private function createOwner(): Owner
    {
        $o = new Owner(
            new OwnerName("Bubu"),
            new OwnerPhone("+79993334444"),
            new OwnerAddress("Haha benis again"),
            new OwnerEmail("otis@alaska.cat")
        );
        $o->setId(new OwnerId(35));
        return $o;
    }

    private function createPatient(Owner $owner, int $id = 36): Patient
    {
        $p = new Patient(
            new PatientName("A good boy"),
            PatientBirthDate::fromString("1960-03-05"),
            new PatientSpecies("Husky")
        );
        $p->setId(new PatientId($id));
        $p->setOwner($owner);
        return $p;
    }

    public function testGetPatients(): void
    {
        $owner = $this->createOwner();
        static::assertEmpty($owner->getPatients());
        $patient1 = $this->createPatient($owner, 36);
        $patient2 = $this->createPatient($owner, 38);
        $patient3 = $this->createPatient($owner, 40);
        static::assertCount(3, $owner->getPatients());
    }

    public function testAddPatient(): void
    {
        $owner = $this->createOwner();
        static::assertEmpty($owner->getPatients());
        $patient = $this->createPatient($owner);
        static::assertCount(1, $owner->getPatients());
    }

    public function testRemovePatient(): void
    {
        $owner = $this->createOwner();
        static::assertEmpty($owner->getPatients());
        $patient = $this->createPatient($owner);
        static::assertCount(1, $owner->getPatients());
        $owner->removePatient($patient->getId());
        static::assertEmpty($owner->getPatients());
    }

    public function testGetName(): void
    {
        $owner = $this->createOwner();
        static::assertNotNull($owner->getName());
        static::assertInstanceOf(OwnerName::class, $owner->getName());
    }

    public function testGetPhone(): void
    {
        $owner = $this->createOwner();
        static::assertNotNull($owner->getPhone());
        static::assertInstanceOf(OwnerPhone::class, $owner->getPhone());
    }

    public function testGetAddress(): void
    {
        $owner = $this->createOwner();
        static::assertNotNull($owner->getAddress());
        static::assertInstanceOf(OwnerAddress::class, $owner->getAddress());
    }

    public function testGetRegisteredAt(): void
    {
        $owner = $this->createOwner();
        static::assertNotNull($owner->getRegisteredAt());
        static::assertInstanceOf(OwnerRegisteredAt::class, $owner->getRegisteredAt());
    }
}
