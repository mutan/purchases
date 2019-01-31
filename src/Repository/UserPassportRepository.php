<?php

namespace App\Repository;

use App\Entity\UserPassport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UserPassport|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserPassport|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserPassport[]    findAll()
 * @method UserPassport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserPassportRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserPassport::class);
    }

    // /**
    //  * @return UserPassport[] Returns an array of UserPassport objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserPassport
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
