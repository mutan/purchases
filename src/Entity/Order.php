<?php

namespace App\Entity;

use App\Services\Entity\OrderHelper;
use DateTimeInterface;
use InvalidArgumentException;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use App\Entity\Traits\TimestampableEntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Interfaces\PrefixableEntityInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderRepository")
 * @ORM\Table(name="`order`")
 * @ORM\HasLifecycleCallbacks
 */
class Order implements PrefixableEntityInterface
{
    use TimestampableEntityTrait;

    const PREFIX = 'O'; //Order

    const STATUS_NEW = 'new';
    const STATUS_APPROVED = 'approved'; // утвержден клиентом
    const STATUS_REDEEMED = 'redeemed'; // в процессе выкупа
    const STATUS_BOUGHT = 'bought'; // выкуплен
    const STATUS_SENT = 'sent'; // отправлен клиенту
    const STATUS_RECEIVED = 'received'; // получен клиентом
    const STATUS_CANCELED = 'canceled'; // отменен клиентом, виден клиенту
    const STATUS_DELETED = 'deleted'; // удален по инициативе клиента, не виден клиенту

    const ALLOWED_STATUSES = [
        self::STATUS_NEW, self::STATUS_APPROVED, self::STATUS_REDEEMED, self:: STATUS_BOUGHT,
        self::STATUS_SENT, self::STATUS_RECEIVED, self::STATUS_CANCELED, self::STATUS_DELETED,
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
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="ordersByManager")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     */
    private $manager;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Product", mappedBy="order", orphanRemoval=true)
     */
    private $products;

    /**
     * @ORM\Column(type="string", length=255, options={"comment":"Магазин"})
     * @Assert\NotBlank()
     */
    private $shop;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"comment":"Комментарий пользователя"})
     */
    private $userComment;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=2, nullable=true, options={"comment":"Доставка по США, $"})
     * @Assert\GreaterThanOrEqual(value=0, groups={"edit_by_manager"})
     */
    private $deliveryToStock;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=2, nullable=true, options={"comment":"Доставка в Россию за кг, $"})
     * @Assert\GreaterThanOrEqual(value=0, groups={"edit_by_manager"})
     */
    private $deliveryToRussiaPerKg;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=2, nullable=true, options={"comment":"Доставка по России, руб"})
     * @Assert\GreaterThanOrEqual(value=0, groups={"edit_by_manager"})
     */
    private $deliveryToClient;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=2, nullable=true, options={"comment":"Доп. платежи"})
     * @Assert\GreaterThanOrEqual(value=0, groups={"edit_by_manager"})
     */
    private $additionalCost;

    /**
     * @ORM\Column(type="text", nullable=true, options={"comment":"Комментарий к доп. платежам"})
     */
    private $additionalCostComment;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=2, nullable=true, options={"comment":"Курс $"})
     * @Assert\GreaterThanOrEqual(value=0, groups={"edit_by_manager"})
     */
    private $rate;

    /**
     * @ORM\Column(type="boolean", options={"default"=false, "comment":"Курс окончательный?"})
     */
    private $isRateFinal = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"comment":"Трекинг-номер"})
     * @Assert\Length(min=2, max=250, minMessage="~min", maxMessage="~max")
     */
    private $tracking;

    /**
     * @ORM\Column(type="string", length=255, options={"default" = Order::STATUS_NEW})
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

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getManager(): ?User
    {
        return $this->manager;
    }

    public function setManager(User $manager): self
    {
        $this->manager = $manager;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        if (!in_array($status, self::ALLOWED_STATUSES)) {
            throw new InvalidArgumentException("Invalid order status");
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
            $product->setOrder($this);
        }
        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            // set the owning side to null (unless already changed)
            if ($product->getOrder() === $this) {
                $product->setOrder(null);
            }
        }
        return $this;
    }

    public function getBoughtDate(): ?DateTimeInterface
    {
        return $this->boughtDate;
    }

    public function setBoughtDate(?DateTimeInterface $boughtDate): self
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

    public function getDeliveryToRussiaPerKg(): ?float
    {
        return $this->deliveryToRussiaPerKg;
    }

    public function setDeliveryToRussiaPerKg(?float $deliveryToRussiaPerKg): self
    {
        $this->deliveryToRussiaPerKg = $deliveryToRussiaPerKg;
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

    public function getRate(): ?float
    {
        return $this->rate;
    }

    public function setRate(?float $rate): self
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

    public function getPrefix(): string
    {
        return self::PREFIX;
    }

    public function getIdWithPrefix(): string
    {
        return $this->getPrefix() . $this->getId();
    }

    public function __toString()
    {
        return $this->getIdWithPrefix();
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
            $sum += $product->getTotal();
        }
        return $sum;
    }


    public function getProductsSumRub()
    {
        $sum = 0;
        foreach ($this->getProducts() as $product) {
            $sum += $product->getTotalRub();
        }
        return $sum;
    }

    /**
     * Суммарный вес всех товаров в заказе
     */
    public function getProductsWeightTotal()
    {
        $sum = 0;
        foreach ($this->getProducts() as $product) {
            $sum += ($product->getWeight() ? $product->getWeight() : $product->getExpectedWeight());
        }
        return $sum;
    }

    public function getDeliveryToStockRub()
    {
        return ceil($this->getDeliveryToStock() * $this->getRate());
    }

    /**
     * Итоговая доставка в Россию $ = вес всех товаров в заказе в кг * доставка в Россию за кг
     */
    public function getDeliveryToRussia()
    {
        return round($this->getProductsWeightTotal() / 1000 * $this->getDeliveryToRussiaPerKg(), 2);
    }

    /**
     * Итоговая доставка в Россию руб. = Итоговая доставка в Россию $ * курс $
     */
    public function getDeliveryToRussiaRub()
    {
        return ceil($this->getDeliveryToRussia() * $this->getRate());
    }

    public function getAdditionalCostRub()
    {
        return ceil($this->getAdditionalCost() * $this->getRate());
    }

    public function getTotalRub()
    {
        return $this->getProductsSumRub()
             + $this->getDeliveryToStockRub()
             + $this->getDeliveryToRussiaRub()
             + $this->getDeliveryToClient()
             + $this->getAdditionalCostRub();
    }

    public function hasProducts() {
        return !$this->getProducts()->isEmpty();
    }

    public function getStatusLabel() {
        return OrderHelper::STATUSES[$this->getStatus()]['label'];
    }

    public function getStatusDescription() {
        return OrderHelper::STATUSES[$this->getStatus()]['description'];
    }
}
