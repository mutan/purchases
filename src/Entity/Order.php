<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderRepository")
 */
class Order
{
    use TimestampableEntityTrait;

    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    const ALLOWED_STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE,
    ];

    const ACTIVE_REASON_NEW      = 'new'; // новый, пользователь может править
    const ACTIVE_REASON_REDEEMED = 'redeemed'; // начат выкуп, пользователь не может править
    const ACTIVE_REASON_BOUGHT   = 'bought'; // выкуплен, едет ко мне
    const ACTIVE_REASON_RECEIVED = 'received'; // получен клиентом

    const ALLOWED_ACTIVE_REASONS = [

    ];

    const INACTIVE_REASON_CANCELED = 'canceled';

    const ALLOWED_INACTIVE_REASONS = [

    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $active_reason;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $inactive_reason;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdWithPrefix()
    {
        return 'O' . $this->id;
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getActiveReason(): ?string
    {
        return $this->active_reason;
    }

    public function setActiveReason(string $active_reason): self
    {
        $this->active_reason = $active_reason;

        return $this;
    }

    public function getInactiveReason(): ?string
    {
        return $this->inactive_reason;
    }

    public function setInactiveReason(?string $inactive_reason): self
    {
        $this->inactive_reason = $inactive_reason;

        return $this;
    }
}
