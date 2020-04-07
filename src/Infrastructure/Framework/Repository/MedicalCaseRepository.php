<?php

namespace App\Infrastructure\Framework\Repository;

use App\Infrastructure\Framework\Entity\MedicalCase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method MedicalCase|null find($id, $lockMode = null, $lockVersion = null)
 * @method MedicalCase|null findOneBy(array $criteria, array $orderBy = null)
 * @method MedicalCase[]    findAll()
 * @method MedicalCase[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MedicalCaseRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManagerInterface;

    public function __construct(
        ManagerRegistry $registry,
        EntityManagerInterface $entityManagerInterface
        )
    {
        parent::__construct($registry, MedicalCase::class);
        $this->entityManagerInterface = $entityManagerInterface;
    }

    /**
     * Updates given medical cases in the DB
     *
     * @param MedicalCase[] $cases
     * @return void
     */
    public function updateCases(array $cases)
    {
        $this->entityManagerInterface->flush();
    }

    // /**
    //  * @return MedicalCase[] Returns an array of MedicalCase objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MedicalCase
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
