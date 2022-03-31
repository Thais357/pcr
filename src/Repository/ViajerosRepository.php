<?php

namespace App\Repository;

use App\Entity\Viajeros;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Viajeros|null find($id, $lockMode = null, $lockVersion = null)
 * @method Viajeros|null findOneBy(array $criteria, array $orderBy = null)
 * @method Viajeros[]    findAll()
 * @method Viajeros[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ViajerosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Viajeros::class);
    }

    // /**
    //  * @return Viajeros[] Returns an array of Viajeros objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Viajeros
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
