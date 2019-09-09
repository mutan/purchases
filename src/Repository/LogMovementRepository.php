<?php

namespace App\Repository;

use App\Entity\LogMovement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LogMovement|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogMovement|null findOneBy(array $criteria, array $orderBy = null)
 * @method LogMovement[]    findAll()
 * @method LogMovement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogMovementRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LogMovement::class);
    }
}
