<?php

namespace App\Domain\Patient\Entity;

use DateTime;
use App\Framework\Entity\Owner as DBALOwner;
use App\Domain\Patient\ValueObject\OwnerId;
use App\Domain\Shared\Entity\IDomainEntity;
use App\Domain\Patient\ValueObject\OwnerName;
use App\Domain\Patient\ValueObject\PatientId;
use App\Domain\Patient\ValueObject\OwnerPhone;
use App\Domain\Patient\ValueObject\OwnerAddress;
use App\Domain\Patient\ValueObject\OwnerRegisteredAt;

class Owner
{
    public function __construct(OwnerName $name, OwnerPhone $phone, OwnerAddress $address)
    {
        $this->name = $name;
        $this->phone = $phone;
        $this->address = $address;
        $this->registeredAt = new OwnerRegisteredAt(new DateTime());
    }

    protected OwnerId $id;
    protected OwnerName $name;
    protected OwnerPhone $phone;
    protected OwnerAddress $address;
    /**
     * @var Patient[]
     */
    protected array $patients = [];
    protected OwnerRegisteredAt $registeredAt;

    public function getId(): OwnerId
    {
        return $this->id;
    }

    public function setId(OwnerId $id): void
    {
        $this->id = $id;
    }

    public function getName(): OwnerName
    {
        return $this->name;
    }

    public function getPhone(): OwnerPhone
    {
        return $this->phone;
    }

    public function getAddress(): OwnerAddress
    {
        return $this->address;
    }

    public function getPatients(): array
    {
        return $this->patients;
    }

    public function addPatient(Patient $patient): void
    {
        $this->patients[] = $patient;
    }

    public function removePatient(PatientId $patientId): void
    {
        $this->patients = array_filter($this->patients, fn ($patient) => $patient->getId() !== $patientId);
    }

    public function getRegisteredAt(): OwnerRegisteredAt
    {
        return $this->registeredAt;
    }
}
