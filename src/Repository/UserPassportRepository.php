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

    /**
     * @param $user
     * @return UserPassport[]
     */
    public function findAllByUser($user)
    {
        return $this->createQueryBuilder('ua')
                    ->andWhere('ua.user = :user')->setParameter('user', $user)
                    ->andWhere('ua.status != :deleted')->setParameter('deleted', UserPassport::STATUS_DELETED)
                    ->getQuery()->getResult();
    }
}
