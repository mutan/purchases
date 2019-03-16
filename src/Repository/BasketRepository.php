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
     * @param $id
     * @return Basket
     */
    public function findWithRelations($id)
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.user', 'bu')->addSelect('bu')
            ->leftJoin('b.manager', 'bm')->addSelect('bm')
            ->leftJoin('b.products', 'bp')->addSelect('bp')
            ->andWhere('b.id = :id')->setParameter('id', $id)
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @param $user
     * @return Basket[]
     */
    public function findAllByUser($user)
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.products', 'p')->addSelect('p')
            ->andWhere('b.user = :user')->setParameter('user', $user)
            ->andWhere('b.status != :deleted')->setParameter('deleted', Basket::STATUS_DELETED)
            ->getQuery()->getResult();
    }

    /**
     * @param $user
     * @return Basket[]
     */
    public function findAllByManager($user)
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.products', 'p')->addSelect('p')
            ->andWhere('b.manager = :user')->setParameter('user', $user)
            ->andWhere('b.status != :deleted')->setParameter('deleted', Basket::STATUS_DELETED)
            ->getQuery()->getResult();
    }
}
