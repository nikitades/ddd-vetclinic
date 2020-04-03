<?php

namespace App\Framework\Entity;

use App\Infrastructure\IEntityAdapter;
use App\Domain\Patient\ValueObject\CardId;
use App\Framework\Entity\Card as DBALCard;
use App\Domain\Patient\ValueObject\OwnerId;
use App\Framework\Entity\Owner as DBALOwner;
use App\Domain\Patient\ValueObject\OwnerName;
use App\Domain\Patient\ValueObject\CardClosed;
use App\Domain\Patient\ValueObject\OwnerPhone;
use App\Domain\Patient\ValueObject\PatientName;
use App\Domain\Patient\ValueObject\OwnerAddress;
use App\Framework\Entity\Patient as DBALPatient;
use App\Domain\Patient\Entity\Card as DomainCard;
use App\Domain\Patient\ValueObject\CardCreatedAt;
use App\Domain\Patient\ValueObject\MedicalCaseId;
use App\Domain\Patient\ValueObject\PatientSpecies;
use App\Domain\Patient\Entity\Owner as DomainOwner;
use App\Domain\Patient\ValueObject\MedicalCaseEnded;
use App\Domain\Patient\ValueObject\PatientBirthDate;
use App\Domain\Patient\ValueObject\OwnerRegisteredAt;
use App\Domain\Patient\ValueObject\MedicalCaseEndedAt;
use App\Domain\Patient\Entity\Patient as DomainPatient;
use App\Domain\Patient\ValueObject\MedicalCaseStartedAt;
use App\Domain\Patient\ValueObject\MedicalCaseTreatment;
use App\Framework\Entity\MedicalCase as DBALMedicalCase;
use App\Domain\Patient\ValueObject\MedicalCaseDescription;
use App\Domain\Patient\Entity\MedicalCase as DomainMedicalCase;

//TODO: test this
class EntityAdapter implements IEntityAdapter
{
    public function fromDomainCard(DomainCard $domainCard): DBALCard
    {
        $domainCard = new DomainCard();
        $dbalCard = new DBALCard();
        $dbalCard->id = $domainCard->getId()->getValue();
        $dbalCard->closed = $domainCard->getClosed()->getValue();
        $dbalCard->createdAt = $domainCard->getCreatedAt()->getValue();
        $dbalCard->patient = $this->fromDomainPatient($domainCard->getPatient());
        $dbalCard->cases = array_map(
            fn ($domainMedicalCase) => $this->fromDomainMedicalCase($domainMedicalCase),
            $domainCard->getCases()
        );
        return $dbalCard;
    }

    public function fromDomainMedicalCase(DomainMedicalCase $domainMedicalCase): DBALMedicalCase
    {
        $dbalMedicalCase = new DBALMedicalCase();
        $dbalMedicalCase->id = $domainMedicalCase->getId()->getValue();
        $dbalMedicalCase->description = $domainMedicalCase->getDescription()->getValue();
        $dbalMedicalCase->treatment = $domainMedicalCase->getTreatment()->getValue();
        $dbalMedicalCase->startedAt = $domainMedicalCase->getStartedAt()->getValue();
        $dbalMedicalCase->endedAt = $domainMedicalCase->getEndedAt()->getValue();
        $dbalMedicalCase->ended = $domainMedicalCase->isEnded()->getValue();
        $dbalMedicalCase->card = $this->fromDomainCard($domainMedicalCase->getCard());
        return $dbalMedicalCase;
    }

    public function fromDomainOwner(DomainOwner $domainOwner): DBALOwner
    {
        $dbalOwner = new DBALOwner();
        $dbalOwner->id = $domainOwner->getId()->getValue();
        $dbalOwner->name = $domainOwner->getName()->getValue();
        $dbalOwner->phone = $domainOwner->getPhone()->getValue();
        $dbalOwner->address = $domainOwner->getAddress()->getValue();
        $dbalOwner->registeredAt = $domainOwner->getRegisteredAt()->getValue();
        $dbalOwner->patients = array_map(
            fn ($domainPatient) => $this->fromDomainPatient($domainPatient),
            $domainOwner->getPatients()
        );
        return $dbalOwner;
    }

    public function fromDomainPatient(DomainPatient $patient): DBALPatient
    {
        $dbalPatient = new DBALPatient();
        $dbalPatient->id = $patient->getId()->getValue();
        $dbalPatient->name = $patient->getName()->getValue();
        $dbalPatient->species->patient->getSpecies()->getValue();
        $dbalPatient->birthDate = $patient->getBirthDate()->getValue();
        $dbalPatient->owner = new Owner($patient->getOwner());
        $dbalPatient->cards = array_map(
            fn ($domainCard) => $this->fromDomainCard($domainCard),
            $patient->getCards()
        );
        return $dbalPatient;
    }

    public function fromDBALCard(DBALCard $dbalCard): DomainCard
    {
        $domainCard = new DomainCard();
        $domainCard->closed = new CardClosed($dbalCard->getClosed());
        $domainCard->createdAt = new CardCreatedAt($dbalCard->getCreatedAt());
        $domainCard->id = new CardId($dbalCard->getId());
        $domainCard->patient = $this->fromDBALPatient($dbalCard->getPatient());
        $domainCard->cases = array_map(
            fn ($dbalMedicalCase) => $this->fromDBALMedicalCase($dbalMedicalCase),
            $dbalCard->getCases()->toArray()
        );
        return $domainCard;
    }

    public function fromDBALMedicalCase(DBALMedicalCase $dbalMedicalCase): DomainMedicalCase
    {
        $domainMedicalCase = new DomainMedicalCase();
        $domainMedicalCase->id = new MedicalCaseId($dbalMedicalCase->getId());
        $domainMedicalCase->description = new MedicalCaseDescription($dbalMedicalCase->getDescription());
        $domainMedicalCase->treatment = new MedicalCaseTreatment($dbalMedicalCase->getTreatment());
        $domainMedicalCase->startedAt = new MedicalCaseStartedAt($dbalMedicalCase->getStartedAt());
        $domainMedicalCase->endedAt = new MedicalCaseEndedAt($dbalMedicalCase->getEndedAt());
        $domainMedicalCase->ended = new MedicalCaseEnded($dbalMedicalCase->getEnded());
        $domainMedicalCase->card = $this->fromDBALCard($dbalMedicalCase->getCard());
        return $domainMedicalCase;
    }

    public function fromDBALOwner(DBALOwner $dbalOwner): DomainOwner
    {
        $domainOwner = new DomainOwner(
            new OwnerName($dbalOwner->getName()),
            new OwnerPhone($dbalOwner->getPhone()),
            new OwnerAddress($dbalOwner->getAddress())
        );
        $domainOwner->id = new OwnerId($dbalOwner->getId());
        $domainOwner->registeredAt = new OwnerRegisteredAt($dbalOwner->getRegisteredAt());
        $domainOwner->patients = array_map(
            fn ($dbalPatient) => $this->fromDBALPatient($dbalPatient),
            $dbalOwner->getPatients()->toArray()
        );
        return $domainOwner;
    }

    public function fromDBALPatient(DBALPatient $dbalPatient): DomainPatient
    {
        $domainPatient = new DomainPatient(
            new PatientName($dbalPatient->getName()),
            new PatientBirthDate($dbalPatient->getBirthDate()),
            new PatientSpecies($dbalPatient->getSpecies())
        );
        $domainPatient->id = $dbalPatient->getId();
        $domainPatient->owner = $this->fromDBALOwner($dbalPatient->getOwner());
        $domainPatient->cards = array_map(
            fn ($dbalCard) => $this->fromDBALCard($dbalCard),
            $dbalPatient->getCards()->toArray()
        );
        return $domainPatient;
    }
}
