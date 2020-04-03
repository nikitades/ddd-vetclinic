<?php

namespace App\Framework\Repository;

use App\Framework\Entity\MedicalCase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method MedicalCase|null find($id, $lockMode = null, $lockVersion = null)
 * @method MedicalCase|null findOneBy(array $criteria, array $orderBy = null)
 * @method MedicalCase[]    findAll()
 * @method MedicalCase[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MedicalCaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MedicalCase::class);
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
