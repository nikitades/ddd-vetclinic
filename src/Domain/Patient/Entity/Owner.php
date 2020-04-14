<?php

namespace App\Domain\Patient\Entity;

use DateTime;
use App\Domain\Patient\ValueObject\OwnerId;
use App\Domain\Patient\ValueObject\OwnerName;
use App\Domain\Patient\ValueObject\PatientId;
use App\Domain\Patient\ValueObject\OwnerEmail;
use App\Domain\Patient\ValueObject\OwnerPhone;
use App\Domain\Patient\ValueObject\OwnerAddress;
use App\Domain\Patient\ValueObject\OwnerRegisteredAt;
use App\Infrastructure\Framework\Repository\OwnerRepository;
use App\Domain\Patient\ValueObject\OwnerNotificationRequired;

class Owner
{
    public function __construct(OwnerName $name, OwnerPhone $phone, OwnerAddress $address, OwnerEmail $email)
    {
        $this->name = $name;
        $this->phone = $phone;
        $this->address = $address;
        $this->email = $email;
        $this->notificationRequired = OwnerNotificationRequired::off();
        $this->registeredAt = new OwnerRegisteredAt(new DateTime());
    }

    protected ?OwnerId $id = null;
    protected OwnerName $name;
    protected OwnerPhone $phone;
    protected OwnerAddress $address;
    protected OwnerEmail $email;
    /** @var Patient[] */
    protected array $patients = [];
    protected OwnerRegisteredAt $registeredAt;
    protected OwnerNotificationRequired $notificationRequired;

    public function getId(): ?OwnerId
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

    public function getEmail(): OwnerEmail
    {
        return $this->email;
    }

    public function setEmail(OwnerEmail $value): void
    {
        $this->email = $value;
    }

    /**
     * Provides the patients list
     *
     * @return Patient[]
     */
    public function getPatients(): array
    {
        return $this->patients;
    }

    /**
     * Sets patients equal to the given list
     *
     * @param Patient[] $patients
     * @return void
     */
    public function setPatients(array $patients): void
    {
        $this->patients = $patients;
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

    public function setRegisteredAt(OwnerRegisteredAt $value): void
    {
        $this->registeredAt = $value;
    }

    public function getNotificationRequired(): OwnerNotificationRequired
    {
        return $this->notificationRequired;
    }

    public function enableNotification(): void
    {
        $this->notificationRequired = OwnerNotificationRequired::on();
    }

    public function disableNotification(): void
    {
        $this->notificationRequired = OwnerNotificationRequired::off();
    }
}
