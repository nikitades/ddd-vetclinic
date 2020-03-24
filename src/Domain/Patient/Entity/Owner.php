<?php

namespace App\Domain\Patient\Entity;

use DateTime;
use App\Domain\Patient\ValueObject\OwnerId;
use App\Domain\Patient\ValueObject\OwnerName;
use App\Domain\Patient\ValueObject\PatientId;
use App\Domain\Patient\ValueObject\OwnerPhone;
use App\Domain\Patient\ValueObject\OwnerAddress;
use App\Domain\Patient\ValueObject\OwnerRegisteredAt;

class Owner
{
    
    public function __construct(OwnerId $id, OwnerName $name, OwnerPhone $phone, OwnerAddress $address)
    {
        $this->id = $id;
        $this->name = $name;
        $this->phone = $phone;
        $this->address = $address;
        $this->registeredAt = new OwnerRegisteredAt(new DateTime());
    }

    protected OwnerId $id;

    public function getId(): OwnerId
    {
        return $this->id;
    }

    protected OwnerName $name;

    public function getName(): OwnerName
    {
        return $this->name;
    }

    protected OwnerPhone $phone;

    public function getPhone(): OwnerPhone
    {
        return $this->phone;
    }

    protected OwnerAddress $address;

    public function getAddress(): OwnerAddress
    {
        return $this->address;
    }

    /**
     * @var Patient[]
     */
    protected array $patients = [];

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

    protected OwnerRegisteredAt $registeredAt;

    public function getRegisteredAt(): OwnerRegisteredAt
    {
        return $this->registeredAt;
    }
}
