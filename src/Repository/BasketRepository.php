<?php

namespace App\Repository;

use App\Entity\Basket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Basket|null find($id, $lockMode = null, $lockVersion = null)
 * @method Basket|null findOneBy(array $criteria, array $orderBy = null)
 * @method Basket[]    findAll()
 * @method Basket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BasketRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Basket::class);
    }

    /**
     * @param $user
     * @return Basket[]
     */
    public function findAllByUser($user)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.user = :user')->setParameter('user', $user)
            ->andWhere('b.status != :deleted')->setParameter('deleted', Basket::STATUS_DELETED)
            ->getQuery()->getResult();
    }
}
