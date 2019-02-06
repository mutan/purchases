<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BasketRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Basket
{
    use TimestampableTrait;

    const STATUS_NEW       = 'new';
    const STATUS_REDEEMED  = 'redeemed';
    const STATUS_BOUGHT    = 'bought';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_DELETED   = 'deleted';

    const ALLOWED_STATUSES = [
        self::STATUS_NEW,
        self::STATUS_REDEEMED,
        self::STATUS_BOUGHT,
        self::STATUS_CANCELLED,
        self::STATUS_DELETED,
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="baskets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Product", mappedBy="basket", orphanRemoval=true)
     */
    private $products;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $boughtDate;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

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
        if (!in_array($status, self::ALLOWED_STATUSES)) {
            throw new \InvalidArgumentException("Invalid basket status");
        }

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

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setBasket($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            // set the owning side to null (unless already changed)
            if ($product->getBasket() === $this) {
                $product->setBasket(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getIdWithPrefix();
    }

    public function getCreateDate(): ?\DateTimeInterface
    {
        return $this->createDate;
    }

    public function setCreateDate(?\DateTimeInterface $createDate): self
    {
        $this->createDate = $createDate;

        return $this;
    }

    public function getBoughtDate(): ?\DateTimeInterface
    {
        return $this->boughtDate;
    }

    public function setBoughtDate(?\DateTimeInterface $boughtDate): self
    {
        $this->boughtDate = $boughtDate;

        return $this;
    }
}
