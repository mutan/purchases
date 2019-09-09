<?php

namespace App\Services;

use App\Entity\LogMovement;
use App\Entity\Order;
use App\Entity\Package;
use App\Entity\Product;
use App\Entity\User;
use App\Entity\UserAddress;
use App\Entity\UserPassport;
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

    /**
     * @param $type
     * @param Product $product
     * @param User|null $user
     * @param null $customMessage
     * @param LogMovement|null $event
     * @throws Exception
     */
    public function addEventForProduct($type, Product $product, User $user = null, $customMessage = null, LogMovement $event = null): void
    {
        if (!$event) {
            $event = new LogMovement();
        }
        $event->setProduct($product);
        $this->addEvent($type, $user, $event, $customMessage);
    }

    /**
     * @param $type
     * @param Package $package
     * @param User|null $user
     * @param null $customMessage
     * @param LogMovement|null $event
     * @throws Exception
     */
    public function addEventForPackage($type, Package $package, User $user = null, $customMessage = null, LogMovement $event = null): void
    {
        if (!$event) {
            $event = new LogMovement();
        }
        $event->setPackage($package);
        $this->addEvent($type, $user, $event, $customMessage);
    }

    /**
     * @param $type
     * @param User $user
     * @param null $customMessage
     * @param LogMovement|null $event
     * @throws Exception
     */
    public function addEventForUser($type, User $user, $customMessage = null, LogMovement $event = null): void
    {
        if (!$event) {
            $event = new LogMovement();
        }
        $event->setUser($user);
        $this->addEvent($type, null, $event, $customMessage);
    }

    /**
     * @param $type
     * @param UserAddress $userAddress
     * @param User|null $user
     * @param null $customMessage
     * @param LogMovement|null $event
     * @throws Exception
     */
    public function addEventForUserAddress($type, UserAddress $userAddress, User $user = null, $customMessage = null, LogMovement $event = null): void
    {
        if (!$event) {
            $event = new LogMovement();
        }
        $event->setUserAddress($userAddress);
        $this->addEvent($type, $user, $event, $customMessage);
    }

    /**
     * @param $type
     * @param UserPassport $userPassport
     * @param User|null $user
     * @param null $customMessage
     * @param LogMovement|null $event
     * @throws Exception
     */
    public function addEventForUserPassport($type, UserPassport $userPassport, User $user = null, $customMessage = null, LogMovement $event = null): void
    {
        if (!$event) {
            $event = new LogMovement();
        }
        $event->setUserPassport($userPassport);
        $this->addEvent($type, $user, $event, $customMessage);
    }
}
