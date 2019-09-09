<?php

namespace App\Services;

use App\Entity\LogMovement;
use App\Entity\Order;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class LogMovementService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param $type
     * @param User|null $user
     * @param LogMovement|null $event
     * @param null $customMessage
     * @throws Exception
     */
    public function addEvent($type, User $user = null, LogMovement $event = null, $customMessage = null): void
    {
        if (!isset(LogMovement::TYPES_MAP[$type])) {
            throw new Exception('Wrong log movement type');
        }
        if (!$event) {
            $event = new LogMovement();
        }
        $event->setType($type)
            ->setMessage($customMessage ?: LogMovement::TYPES_MAP[$type])
            ->setCreateDate(new DateTime())
            ->setUser($user);
        $this->em->persist($event);
        $this->em->flush();
    }

    /**
     * @param $type
     * @param Order $order
     * @param User|null $user
     * @param null $customMessage
     * @param LogMovement|null $event
     * @throws Exception
     */
    public function addEventForOrder($type, Order $order, User $user = null, $customMessage = null, LogMovement $event = null): void
    {
        if (!$event) {
            $event = new LogMovement();
        }
        $event->setOrder($order);
        $this->addEvent($type, $user, $event, $customMessage);
    }
}