<?php

namespace App\Repository;

use App\Entity\Etudant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Etudant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Etudant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Etudant[]    findAll()
 * @method Etudant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EtudantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Etudant::class);
    }

    // /**
    //  * @return Etudant[] Returns an array of Etudant objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Etudant
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
