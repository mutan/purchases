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

    const STATUS_NEW      = 'new';
    const STATUS_REDEEMED = 'redeemed'; // в процессе выкупа
    const STATUS_BOUGHT   = 'bought'; // выкуплен
    const STATUS_SENT     = 'sent'; // отправлен пользователю
    const STATUS_RECEIVED = 'received'; // получен пользователем
    const STATUS_CANCELED = 'canceled'; // отменен пользователем, виден пользователю
    const STATUS_DELETED  = 'deleted'; // удален по инициативе пользователя, не виден пользователю

    const ALLOWED_STATUSES = [
        self::STATUS_NEW, self::STATUS_REDEEMED, self:: STATUS_BOUGHT,
        self::STATUS_SENT, self::STATUS_RECEIVED, self::STATUS_CANCELED, self::STATUS_DELETED,
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
     * @Assert\Url()
     */
    private $shop;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(min=2, max=250, minMessage="~min", maxMessage="~max")
     */
    private $userComment;

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
     * @ORM\Column(type="decimal", precision=7, scale=2, nullable=true)
     */
    private $rate;

    /**
     * @ORM\Column(type="boolean", options={"default"=false})
     */
    private $isRateFinal = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tracking;

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

    public function getShop(): ?string
    {
        return $this->shop;
    }

    public function setShop(?string $shop): self
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

    public function getRate(): ?float
    {
        return $this->rate;
    }

    public function setRate(float $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    public function getIsRateFinal(): ?bool
    {
        return $this->isRateFinal;
    }

    public function setIsRateFinal(bool $isRateFinal): self
    {
        $this->isRateFinal = $isRateFinal;

        return $this;
    }

    public function getTracking(): ?string
    {
        return $this->tracking;
    }

    public function setTracking(?string $tracking): self
    {
        $this->tracking = $tracking;

        return $this;
    }

    /* ADDITIONAL METHODS */

    public function __toString()
    {
        return $this->getIdWithPrefix();
    }

    public function getIdWithPrefix()
    {
        return 'OR' . $this->id;
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

    public function isCanceled()
    {
        return $this->getStatus() == self::STATUS_CANCELED;
    }

    public function isDeleted()
    {
        return $this->getStatus() == self::STATUS_DELETED;
    }

    public function getProductsAmount()
    {
        $amount = 0;
        foreach ($this->getProducts() as $product) {
            $amount += $product->getAmount();
        }

        return $amount;
    }

    public function getProductsSum()
    {
        $sum = 0;
        foreach ($this->getProducts() as $product) {
            $sum += $product->getSum();
        }

        return $sum;
    }

    public function getProductsSumRub()
    {
        $sum = 0;
        foreach ($this->getProducts() as $product) {
            $sum += $product->getSumRub();
        }

        return $sum;
    }

    public function getDeliveryToStockRub()
    {
        return ceil($this->getDeliveryToStock() * $this->getRate());
    }

    public function getDeliveryToRussiaRub()
    {
        return ceil($this->getDeliveryToRussia() * $this->getRate());
    }

    public function getTotalRub()
    {
        return $this->getProductsSumRub() + $this->getDeliveryToStockRub() + $this->getDeliveryToRussiaRub() + $this->getDeliveryToClient();
    }
}
