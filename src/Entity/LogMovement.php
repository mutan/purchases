<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LogMovementRepository")
 */
class LogMovement
{
    const ORDER_CREATED = 101;
    const ORDER_DELETED = 102;
    const ORDER_STATUS_CHANGED = 103;

    const PRODUCT_CREATED = 201;
    const PRODUCT_DELETED = 202;

    const PACKAGE_CREATED = 301;

    const USER_CREATED = 401;

    const USER_ADDRESS_CREATED = 501;

    const USER_PASSPORT_CREATED = 601;

    const TYPES_MAP = [
        self::ORDER_CREATED => 'Заказ содан',
        self::ORDER_DELETED => 'Заказ удален',
        self::ORDER_STATUS_CHANGED => 'Статус заказа изменен',

        self::PRODUCT_CREATED => 'Товар создан',
        self::PRODUCT_DELETED => 'Товар удален',

        self::PACKAGE_CREATED => 'Посылка создана',

        self::USER_CREATED => 'Пользователь создан',

        self::USER_ADDRESS_CREATED => 'Адрес пользователя создан',

        self::USER_PASSPORT_CREATED => 'Пасспорт пользователя создан',
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $message;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Order")
     */
    private $order;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product")
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Package")
     */
    private $package;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserAddress")
     */
    private $userAddress;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserPassport")
     */
    private $userPassport;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreateDate(): ?DateTimeInterface
    {
        return $this->createDate;
    }

    public function setCreateDate(DateTimeInterface $createDate): self
    {
        $this->createDate = $createDate;
        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): self
    {
        $this->order = $order;
        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;
        return $this;
    }

    public function getPackage(): ?Package
    {
        return $this->package;
    }

    public function setPackage(?Package $package): self
    {
        $this->package = $package;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getUserAddress(): ?UserAddress
    {
        return $this->userAddress;
    }

    public function setUserAddress(?UserAddress $userAddress): self
    {
        $this->userAddress = $userAddress;
        return $this;
    }

    public function getUserPassport(): ?UserPassport
    {
        return $this->userPassport;
    }

    public function setUserPassport(?UserPassport $userPassport): self
    {
        $this->userPassport = $userPassport;
        return $this;
    }
}
