<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PackageRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Package
{
    use TimestampableTrait;

    const STATUS_NEW              = 'new';
    const STATUS_WAITING_ARRIVAL  = 'waiting_arrival'; // ожидается менеджером СП
    const STATUS_RECEIVED         = 'recieved'; // получена менеджером СП

    const ALLOWED_STATUSES = [
        self::STATUS_NEW, self::STATUS_WAITING_ARRIVAL, self::STATUS_RECEIVED,
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, options={"comment":"Название"})
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true, options={"comment":"Комментарий менеджера"})
     */
    private $comment;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"comment":"Вес от форвардера"})
     */
    private $weight;

    /**
     * @ORM\Column(type="float", nullable=true, options={"comment":"Стоимость от форвардера"})
     */
    private $deliveryCost;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tracking;

    /**
     * @ORM\Column(type="string", length=255, options={"default" = Package::STATUS_NEW})
     */
    private $status = self::STATUS_NEW;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(?int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getDeliveryCost(): ?float
    {
        return $this->deliveryCost;
    }

    public function setDeliveryCost(?float $deliveryCost): self
    {
        $this->deliveryCost = $deliveryCost;

        return $this;
    }

    public function getTracking(): ?string
    {
        return $this->tracking;
    }

    public function setTracking(string $tracking): self
    {
        $this->tracking = $tracking;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        if (!in_array($status, self::ALLOWED_STATUSES)) {
            throw new \InvalidArgumentException("Invalid package status");
        }

        $this->status = $status;

        return $this;
    }

    /* ADDITIONAL METHODS */

    public function __toString()
    {
        return $this->getIdWithPrefix();
    }

    public function getIdWithPrefix()
    {
        return 'PA' . $this->id;
    }
}
