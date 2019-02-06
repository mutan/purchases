<?php

namespace App\Repository;

use App\Entity\UserAddress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UserAddress|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserAddress|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserAddress[]    findAll()
 * @method UserAddress[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserAddressRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserAddress::class);
    }

    /**
     * @param $user
     * @return UserAddress[]
     */
    public function findAllByUser($user)
    {
        return $this->createQueryBuilder('ua')
                    ->andWhere('ua.user = :user')->setParameter('user', $user)
                    ->andWhere('ua.status != :deleted')->setParameter('deleted', UserAddress::STATUS_DELETED)
                    ->getQuery()->getResult();
    }
}
