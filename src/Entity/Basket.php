<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
    const STATUS_SENT      = 'sent';
    const STATUS_RECEIVED  = 'received';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_DELETED   = 'deleted';

    const ALLOWED_STATUSES = [
        self::STATUS_NEW,
        self::STATUS_REDEEMED,
        self::STATUS_BOUGHT,
        self::STATUS_SENT,
        self::STATUS_RECEIVED,
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
     * @ORM\OneToMany(targetEntity="App\Entity\Product", mappedBy="basket", orphanRemoval=true)
     */
    private $products;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="~not_blank")
     */
    private $shop;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(min=2, max=250, minMessage="~min", maxMessage="~max")
     */
    private $userComment;

    // TODO не нужна?
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $weight;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $deliveryToStock;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $deliveryToRussia;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $deliveryToClient;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $additionalCost;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $additionalCostComment;

    /**
     * @ORM\Column(type="string", length=255, options={"default" = Basket::STATUS_NEW})
     */
    private $status = self::STATUS_NEW;

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

      public function getBoughtDate(): ?\DateTimeInterface
    {
        return $this->boughtDate;
    }

    public function setBoughtDate(?\DateTimeInterface $boughtDate): self
    {
        $this->boughtDate = $boughtDate;

        return $this;
    }

    public function getDeliveryToStock(): ?float
    {
        return $this->deliveryToStock;
    }

    public function setDeliveryToStock(?float $deliveryToStock): self
    {
        $this->deliveryToStock = $deliveryToStock;

        return $this;
    }

    public function getDeliveryToRussia(): ?float
    {
        return $this->deliveryToRussia;
    }

    public function setDeliveryToRussia(?float $deliveryToRussia): self
    {
        $this->deliveryToRussia = $deliveryToRussia;

        return $this;
    }

    public function getDeliveryToClient(): ?float
    {
        return $this->deliveryToClient;
    }

    public function setDeliveryToClient(?float $deliveryToClient): self
    {
        $this->deliveryToClient = $deliveryToClient;

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

    public function getShop(): ?string
    {
        return $this->shop;
    }

    public function setShop(string $shop): self
    {
        $this->shop = $shop;

        return $this;
    }

    public function getUserComment(): ?string
    {
        return $this->userComment;
    }

    public function setUserComment(?string $userComment): self
    {
        $this->userComment = $userComment;

        return $this;
    }

    public function getAdditionalCost(): ?float
    {
        return $this->additionalCost;
    }

    public function setAdditionalCost(?float $additionalCost): self
    {
        $this->additionalCost = $additionalCost;

        return $this;
    }

    public function getAdditionalCostComment(): ?string
    {
        return $this->additionalCostComment;
    }

    public function setAdditionalCostComment(?string $additionalCostComment): self
    {
        $this->additionalCostComment = $additionalCostComment;

        return $this;
    }

    /* ADDITIONAL METHODS */

    public function __toString()
    {
        return $this->getIdWithPrefix();
    }

    public function getIdWithPrefix()
    {
        return 'BS' . $this->id;
    }

    public function isNew()
    {
        return $this->getStatus() == self::STATUS_NEW;
    }

    public function isRedeemed()
    {
        return $this->getStatus() == self::STATUS_REDEEMED;
    }

    public function isBought()
    {
        return $this->getStatus() == self::STATUS_BOUGHT;
    }

    public function isCancelled()
    {
        return $this->getStatus() == self::STATUS_CANCELLED;
    }

    public function isDeleted()
    {
        return $this->getStatus() == self::STATUS_DELETED;
    }
}
