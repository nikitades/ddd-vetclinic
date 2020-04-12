<?php

namespace App\Test;

use DateTime;
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
use App\Domain\Patient\ValueObject\OwnerRegisteredAt;
use App\Domain\Patient\ValueObject\MedicalCaseTreatment;
use App\Domain\Patient\ValueObject\MedicalCaseDescription;
use Symfony\Component\DependencyInjection\ContainerInterface;

trait EntityGenerator {
    private function createOwner(): Owner
    {
        $owner = new Owner(
            new OwnerName("Jim"),
            new OwnerPhone("+73423948989"),
            new OwnerAddress("Haha st."),
            new OwnerEmail("worm@earth.sega")
        );
        $owner->setId(new OwnerId(43));
        $owner->setRegisteredAt(new OwnerRegisteredAt(new DateTime("1999-12-01")));
        return $owner;
    }

    private function createCard(): Card
    {
        $card = new Card();
        $card->setId(new CardId(44));
        $card->addCase($this->createCase());
        return $card;
    }

    private function createCase(): MedicalCase
    {
        $case = new MedicalCase();
        $case->setDescription(new MedicalCaseDescription("Hoho"));
        $case->setTreatment(new MedicalCaseTreatment("haha"));
        $case->setId(new MedicalCaseId(21));
        return $case;
    }

    private function createPatient(): Patient
    {
        $patient = new Patient(
            new PatientName("Carl"),
            new PatientBirthDate(new DateTime("2000-01-01")),
            new PatientSpecies("Horse")
        );
        $patient->setId(new PatientId(23));
        $patient->addCard($this->createCard());
        $patient->setOwner($this->createOwner());
        return $patient;
    }

    private function getContainer(): ContainerInterface
    {
        static::bootKernel();
        $container = static::$kernel->getContainer();
        return $container;
    }
}