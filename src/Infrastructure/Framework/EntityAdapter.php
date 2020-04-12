<?php

namespace App\Infrastructure\Framework;

use InvalidArgumentException;
use App\Infrastructure\IEntityAdapter;
use App\Domain\Patient\ValueObject\CardId;
use App\Domain\Patient\ValueObject\OwnerId;
use App\Domain\Patient\ValueObject\OwnerName;
use App\Domain\Patient\ValueObject\OwnerEmail;
use App\Domain\Patient\ValueObject\OwnerPhone;
use App\Domain\Patient\ValueObject\PatientName;
use App\Domain\Patient\ValueObject\OwnerAddress;
use Doctrine\Common\Collections\ArrayCollection;
use App\Domain\Patient\Entity\Card as DomainCard;
use App\Domain\Patient\ValueObject\CardCreatedAt;
use App\Domain\Patient\ValueObject\MedicalCaseId;
use App\Domain\Patient\ValueObject\PatientSpecies;
use App\Domain\Patient\Entity\Owner as DomainOwner;
use App\Domain\Patient\ValueObject\PatientBirthDate;
use App\Domain\Patient\ValueObject\OwnerRegisteredAt;
use App\Domain\Patient\ValueObject\MedicalCaseEndedAt;
use App\Domain\Patient\Entity\Patient as DomainPatient;
use App\Domain\Patient\ValueObject\MedicalCaseStartedAt;
use App\Domain\Patient\ValueObject\MedicalCaseTreatment;
use App\Infrastructure\Framework\Entity\Card as DBALCard;
use App\Domain\Patient\ValueObject\MedicalCaseDescription;
use App\Infrastructure\Framework\Entity\Owner as DBALOwner;
use App\Domain\Patient\Entity\MedicalCase as DomainMedicalCase;
use App\Infrastructure\Framework\Entity\Patient as DBALPatient;
use App\Infrastructure\Framework\Entity\MedicalCase as DBALMedicalCase;

class EntityAdapter implements IEntityAdapter
{
    public function fromDomainCard(DomainCard $domainCard): DBALCard
    {
        $domainCard = new DomainCard();
        $dbalCard = new DBALCard();
        $dbalCard->setId($domainCard->getId()->getValue());
        $dbalCard->setCreatedAt($domainCard->getCreatedAt()->getValue());
        $dbalCard->setPatient($this->fromDomainPatient($domainCard->getPatient()));
        $dbalCard->setCases(new ArrayCollection(array_map(
            fn ($domainMedicalCase) => $this->fromDomainMedicalCase($domainMedicalCase),
            $domainCard->getCases()
        )));
        return $dbalCard;
    }

    public function fromDomainMedicalCase(DomainMedicalCase $domainMedicalCase): DBALMedicalCase
    {
        $dbalMedicalCase = new DBALMedicalCase();
        $dbalMedicalCase->setId($domainMedicalCase->getId()->getValue());
        $dbalMedicalCase->setDescription($domainMedicalCase->getDescription()->getValue());
        $dbalMedicalCase->setTreatment($domainMedicalCase->getTreatment()->getValue());
        $dbalMedicalCase->setStartedAt($domainMedicalCase->getStartedAt()->getValue());
        $endedAt = $domainMedicalCase->getEndedAt();
        if (!empty($endedAt)) $dbalMedicalCase->setEndedAt($endedAt->getValue());
        $dbalMedicalCase->setEnded($domainMedicalCase->isEnded()->getValue());
        $dbalMedicalCase->setCard($this->fromDomainCard($domainMedicalCase->getCard()));
        return $dbalMedicalCase;
    }

    public function fromDomainOwner(DomainOwner $domainOwner): DBALOwner
    {
        $dbalOwner = new DBALOwner();
        $dbalOwner->setId($domainOwner->getId()->getValue());
        $dbalOwner->setName($domainOwner->getName()->getValue());
        $dbalOwner->setPhone($domainOwner->getPhone()->getValue());
        $dbalOwner->setAddress($domainOwner->getAddress()->getValue());
        $dbalOwner->setEmail($domainOwner->getEmail()->getValue());
        $dbalOwner->setRegisteredAt($domainOwner->getRegisteredAt()->getValue());
        $dbalOwner->setPatients(new ArrayCollection(array_map(
            fn ($domainPatient) => $this->fromDomainPatient($domainPatient),
            $domainOwner->getPatients()
        )));
        return $dbalOwner;
    }

    public function fromDomainPatient(DomainPatient $patient): DBALPatient
    {
        $dbalPatient = new DBALPatient();
        $dbalPatient->setId($patient->getId()->getValue());
        $dbalPatient->setName($patient->getName()->getValue());
        $dbalPatient->setSpecies($patient->getSpecies()->getValue());
        $dbalPatient->setBirthDate($patient->getBirthDate()->getValue());
        $dbalPatient->setOwner($this->fromDomainOwner($this->halt($patient->getOwner())));
        $dbalPatient->setCards(new ArrayCollection(array_map(
            fn ($domainCard) => $this->fromDomainCard($domainCard),
            $patient->getCards()
        )));
        return $dbalPatient;
    }

    public function fromDBALCard(DBALCard $dbalCard): DomainCard
    {
        $domainCard = new DomainCard();
        $domainCard->setId(new CardId($this->halt($dbalCard->getId())));
        $domainCard->setCreatedAt(new CardCreatedAt($this->halt($dbalCard->getCreatedAt())));
        $domainCard->setPatient($this->fromDBALPatient($this->halt($dbalCard->getPatient())));
        $domainCard->setCases(array_map(
            fn ($dbalMedicalCase) => $this->fromDBALMedicalCase($dbalMedicalCase),
            $dbalCard->getCases()->toArray()
        ));
        return $domainCard;
    }

    public function fromDBALMedicalCase(DBALMedicalCase $dbalMedicalCase): DomainMedicalCase
    {
        $domainMedicalCase = new DomainMedicalCase();
        $domainMedicalCase->setId(new MedicalCaseId($this->halt($dbalMedicalCase->getId())));
        $domainMedicalCase->setDescription(new MedicalCaseDescription($this->halt($dbalMedicalCase->getDescription())));
        $domainMedicalCase->setTreatment(new MedicalCaseTreatment($this->halt($dbalMedicalCase->getTreatment())));
        $domainMedicalCase->setStartedAt(new MedicalCaseStartedAt($this->halt($dbalMedicalCase->getStartedAt())));
        $domainMedicalCase->setEndedAt(new MedicalCaseEndedAt($this->halt($dbalMedicalCase->getEndedAt())));
        if ($dbalMedicalCase->getEnded()) $domainMedicalCase->end();
        $domainMedicalCase->setCard($this->fromDBALCard($this->halt($dbalMedicalCase->getCard())));
        return $domainMedicalCase;
    }

    public function fromDBALOwner(DBALOwner $dbalOwner): DomainOwner
    {
        $domainOwner = new DomainOwner(
            new OwnerName($this->halt($dbalOwner->getName())),
            new OwnerPhone($this->halt($dbalOwner->getPhone())),
            new OwnerAddress($this->halt($dbalOwner->getAddress())),
            new OwnerEmail($this->halt($dbalOwner->getEmail()))
        );
        $domainOwner->setId(new OwnerId($this->halt($dbalOwner->getId())));
        $domainOwner->setRegisteredAt(new OwnerRegisteredAt($this->halt($dbalOwner->getRegisteredAt())));
        $domainOwner->setPatients(array_map(
            fn ($dbalPatient) => $this->fromDBALPatient($dbalPatient),
            $dbalOwner->getPatients()->toArray()
        ));
        return $domainOwner;
    }

    public function fromDBALPatient(DBALPatient $dbalPatient): DomainPatient
    {
        $domainPatient = new DomainPatient(
            new PatientName($this->halt($dbalPatient->getName())),
            new PatientBirthDate($this->halt($dbalPatient->getBirthDate())),
            new PatientSpecies($this->halt($dbalPatient->getSpecies()))
        );
        $domainPatient->setId($this->halt($dbalPatient->getId()));
        $domainPatient->setOwner($this->fromDBALOwner($this->halt($dbalPatient->getOwner())));
        $domainPatient->setCards(array_map(
            fn ($dbalCard) => $this->fromDBALCard($dbalCard),
            $dbalPatient->getCards()->toArray()
        ));
        return $domainPatient;
    }

    /**
     * Checks if the given value is null.
     *
     * @param mixed $value
     * @return mixed
     */
    private function halt(
        /** @var mixed */
        $value
    ) {
        if (is_null($value)) throw new InvalidArgumentException();
        return $value;
    }
}
