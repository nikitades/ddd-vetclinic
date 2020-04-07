<?php

namespace App\Infrastructure;

use App\Infrastructure\Framework\Entity\Card as DBALCard;
use App\Infrastructure\Framework\Entity\Owner as DBALOwner;
use App\Infrastructure\Framework\Entity\Patient as DBALPatient;
use App\Domain\Patient\Entity\Card as DomainCard;
use App\Domain\Patient\Entity\Owner as DomainOwner;
use App\Domain\Patient\Entity\Patient as DomainPatient;
use App\Infrastructure\Framework\Entity\MedicalCase as DBALMedicalCase;
use App\Domain\Patient\Entity\MedicalCase as DomainMedicalCase;

interface IEntityAdapter
{
    public function fromDomainCard(DomainCard $domainCard): DBALCard;
    public function fromDomainMedicalCase(DomainMedicalCase $domainMedicalCase): DBALMedicalCase;
    public function fromDomainOwner(DomainOwner $domainOwner): DBALOwner;
    public function fromDomainPatient(DomainPatient $patient): DBALPatient;
    public function fromDBALCard(DBALCard $dbalCard): DomainCard;
    public function fromDBALMedicalCase(DBALMedicalCase $dbalMedicalCase): DomainMedicalCase;
    public function fromDBALOwner(DBALOwner $dbalOwner): DomainOwner;
    public function fromDBALPatient(DBALPatient $dbalPatient): DomainPatient;
}
