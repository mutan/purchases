<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LogMovementRepository")
 */
class LogMovement
{
    const ORDER_CREATE = 101;

    const PRODUCT_CREATE = 201;

    const PACKAGE_CREATE = 301;

    const USER_CREATE = 401;

    const USER_ADDRESS_CREATE = 501;

    const USER_PASSPORT_CREATE = 601;

    const TYPES_MAP = [
        self::ORDER_CREATE => 'Заказ содан',

        self::PRODUCT_CREATE => 'Товар создан',

        self::PACKAGE_CREATE => 'Посылка создана',

        self::USER_CREATE => 'Пользователь создан',

        self::USER_ADDRESS_CREATE => 'Адрес пользователя создан',

        self::USER_PASSPORT_CREATE => 'Пасспорт пользователя создан',
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
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Order")
     */
    private $order;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product")
     */
    private $product;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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
}
