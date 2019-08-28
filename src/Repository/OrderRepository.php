<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * @param $id
     * @return Order
     * @throws NonUniqueResultException
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
     * @return Order[]
     */
    public function findAllByUser($user)
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.products', 'p')->addSelect('p')
            ->andWhere('b.user = :user')->setParameter('user', $user)
            ->andWhere('b.status != :deleted')->setParameter('deleted', Order::STATUS_DELETED)
            ->getQuery()->getResult();
    }

    /**
     * @param $user
     * @return Order[]
     */
    public function findAllByManager($user)
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.products', 'p')->addSelect('p')
            ->andWhere('b.manager = :user')->setParameter('user', $user)
            ->andWhere('b.status != :deleted')->setParameter('deleted', Order::STATUS_DELETED)
            ->addOrderBy('b.shop')
            ->getQuery()->getResult();
    }
}
