<?php

namespace App\Repository;

use App\Entity\GroupTrick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method GroupTrick|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupTrick|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupTrick[]    findAll()
 * @method GroupTrick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupTrickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupTrick::class);
    }

    // /**
    //  * @return GroupTrick[] Returns an array of GroupTrick objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GroupTrick
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
