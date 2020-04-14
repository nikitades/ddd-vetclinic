<?php

namespace App\Infrastructure\Framework\Repository;

use Doctrine\ORM\EntityManagerInterface;
use App\Infrastructure\Framework\Entity\Patient;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Patient|null find($id, $lockMode = null, $lockVersion = null)
 * @method Patient|null findOneBy(array $criteria, array $orderBy = null)
 * @method Patient[]    findAll()
 * @method Patient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PatientRepository extends ServiceEntityRepository
{

    private EntityManagerInterface $manager;

    public function __construct(
        ManagerRegistry $registry,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($registry, Patient::class);
        $this->manager = $entityManager;
    }

    public function getPatientByNameAndOwnerName(string $patientName, string $ownerName): ?Patient
    {
        $patient = $this->createQueryBuilder('p')
            ->join('p.owner', 'o')
            ->where('o.name = :ownerName')
            ->andWhere('p.name = :patientName')
            ->setParameters(compact('ownerName', 'patientName'))
            ->getQuery()
            ->getOneOrNullResult();
        return $patient;
    }

    public function getPatientByNameAndOwnerId(string $patientName, int $ownerId): ?Patient
    {
        $patient = $this->createQueryBuilder('p')
            ->join('p.owner', 'o')
            ->where('o.id = :ownerId')
            ->andWhere('p.name = :patientName')
            ->setParameters(compact('ownerId', 'patientName'))
            ->getQuery()
            ->getOneOrNullResult();
        return $patient;
    }

    /**
     * Fetches all the patients meeting the criteria from DB
     *
     * @param boolean $onTreatment - whether to include the patients on treatment
     * @param boolean $released - whether to inlcude the released ones
     * @return Patient[]
     */
    public function getAll(bool $onTreatment, bool $released): array
    {
        $query = $this->createQueryBuilder('p')
            ->join('p.cards', 'c')
            ->join('c.cases', 'cas');
        if ($onTreatment) {
            $query->where('cas.ended = false');
        } else if ($released) {
            $query->where('cas.ended = true');
        }
        return $query->getQuery()->getResult();
    }

    /**
     * Fetches all the patients with the name matching the given one
     *
     * @param string $name
     * @return Patient[]
     */
    public function getAllWithName(string $name): array
    {
        $query = $this->createQueryBuilder('p')
            ->where('p.name = :name')
            ->setParameter('name', $name);
        return $query->getQuery()->getResult();
    }

    public function create(Patient $patient): Patient
    {
        $this->manager->persist($patient);
        $this->manager->flush();
        return $patient;
    }

    /**
     * Since we're bound to constant domain model <-> dbal model conversions,
     * Entity Manager's built-in entity tracking mechanism fails. Once having fetched the dbal entity,
     * we send it somewhere to the application level converted to the domain model.
     * Then, being modified, it comes back, again transformed to the dbal entity. And this is another dbal entity instance,
     * not the one Doctrine Entity Manager knows about.
     * 
     * And thus it's not updated. Or in some cases we are getting clones created.
     * So we have to handle updates and removals manually.
     * 
     * Probably should have used something like yaml ORM entity description
     * to use the same class both for domain level and for DBAL.
     *
     * @param Patient $patient
     * @return void
     */
    public function update(Patient $patient): void
    {
        $name = $patient->getName();
        $species = $patient->getSpecies();
        $birthDate = $patient->getBirthDate();
        $ownerId = $patient->getOwnerId();
        $id = $patient->getId();
        $patientClass = Patient::class;
        $dqlQuery = $this->manager->createQuery("UPDATE $patientClass p 
            SET
                p.name = :name,
                p.species = :species,
                p.birthDate = :birthDate,
                p.ownerId = :ownerId
            WHERE p.id = :id")
            ->setParameter("name", $name)
            ->setParameter("species", $species)
            ->setParameter("birthDate", $birthDate)
            ->setParameter("ownerId", $ownerId)
            ->setParameter("id", $id);
        $dqlQuery->execute();
    }

    public function remove(Patient $patient): void
    {
        $id = $patient->getId();
        $this->manager->createQuery("DELETE FROM {$this->getEntityName()} p WHERE p.Id = :id")
            ->setParameter("id", $id)
            ->execute();
    }

    // /**
    //  * @return Patient[] Returns an array of Patient objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Patient
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
