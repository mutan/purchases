<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use InvalidArgumentException;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimestampableEntityTrait;
use App\Entity\Interfaces\PrefixableEntityInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Product implements PrefixableEntityInterface
{
    use TimestampableEntityTrait;

    const PREFIX = 'G'; //Good

    const STATUS_ACTIVE = 'active'; // готов к покупке
    const STATUS_DELETED = 'deleted'; // удален по инициативе пользователя
    const STATUS_CANCELED = 'canceled'; // не удалось купить

    const ALLOWED_STATUSES = [
        self::STATUS_ACTIVE, self::STATUS_DELETED, self::STATUS_CANCELED,
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Order", inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $order;

    /**
     * @ORM\Column(type="string", length=255, options={"comment":"Название товара"})
     * @Assert\NotBlank(message="~not_blank")
     * @Assert\Length(min=1, max=250, minMessage="~min", maxMessage="~max")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=2000, options={"comment":"Ссылка на товар"})
     * @Assert\NotBlank(message="~not_blank")
     * @Assert\Length(min=1, max=2000, minMessage="~min", maxMessage="~max")
     * @Assert\Url()
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"comment":"Артикул или EbayID"})
     * @Assert\Length(min=1, max=250, minMessage="~min", maxMessage="~max")
     */
    private $article;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=2, options={"comment":"Цена, указанная пользователем"})
     * @Assert\NotBlank(message="~not_blank")
     * @Assert\Regex(pattern="/^\d+(\.\d{1,2})?$/", message="~regexp.price")
     * @Assert\GreaterThan(value=0)
     */
    private $userPrice;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=2, nullable=true, options={"comment":"Цена, указанная менеджером"})
     * @Assert\Regex(pattern="/^\d+(\.\d{1,2})?$/", message="~regexp.price")
     * @Assert\GreaterThan(value=0)
     */
    private $price;

    /**
     * @ORM\Column(type="integer", options={"comment":"Кол-во"})
     * @Assert\NotBlank(message="~not_blank")
     * @Assert\Type(type="integer")
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"comment":"Комментарий пользователя"})
     * @Assert\Length(min=2, max=250, minMessage="~min", maxMessage="~max")
     */
    private $comment;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"comment":"Ожидаемый вес"})
     * @Assert\Type(type="integer")
     */
    private $expectedWeight;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"comment":"Окончательный вес"})
     * @Assert\Type(type="integer")
     */
    private $weight;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=2, nullable=true, options={"comment":"Реальная цена выкупа"})
     * @Assert\Regex(pattern="/^\d+(\.\d{1,2})?$/", message="~regexp.price")
     * @Assert\GreaterThan(value=0)
     */
    private $purchasePrice;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"comment":"Реальный магазин выкупа"})
     * @Assert\Length(min=1, max=250, minMessage="~min", maxMessage="~max")
     */
    private $purchaseShop;

    /**
     * @ORM\Column(type="string", length=255, options={"default" = Product::STATUS_ACTIVE})
     */
    private $status = self::STATUS_ACTIVE;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LogMovement", mappedBy="product")
     * @ORM\JoinColumn(onDelete="cascade")
     */
    private $logMovements;

    public function __construct()
    {
        $this->logMovements = new ArrayCollection();
    }

    /**
     * @return Collection|LogMovement[]
     */
    public function getLogMovements(): Collection
    {
        return $this->logMovements;
    }

    public function addLogMovement(LogMovement $logMovement): self
    {
        if (!$this->logMovements->contains($logMovement)) {
            $this->logMovements[] = $logMovement;
            $logMovement->setProduct($this);
        }

        return $this;
    }

    public function removeLogMovement(LogMovement $logMovement): self
    {
        if ($this->logMovements->contains($logMovement)) {
            $this->logMovements->removeElement($logMovement);
            // set the owning side to null (unless already changed)
            if ($logMovement->getProduct() === $this) {
                $logMovement->setProduct(null);
            }
        }

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function getUserPrice(): ?float
    {
        return $this->userPrice;
    }

    public function setUserPrice(?float $userPrice): self
    {
        $this->userPrice = $userPrice;
        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(?int $amount): self
    {
        $this->amount = $amount;
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

    public function getArticle(): ?string
    {
        return $this->article;
    }

    public function setArticle(?string $article): self
    {
        $this->article = $article;
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        if (!in_array($status, self::ALLOWED_STATUSES)) {
            throw new InvalidArgumentException("Invalid product status");
        }

        $this->status = $status;
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

    public function getExpectedWeight(): ?int
    {
        return $this->expectedWeight;
    }

    public function setExpectedWeight(?int $expectedWeight): self
    {
        $this->expectedWeight = $expectedWeight;
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

    public function getPurchasePrice(): ?float
    {
        return $this->purchasePrice;
    }

    public function setPurchasePrice(?float $purchasePrice): self
    {
        $this->purchasePrice = $purchasePrice;
        return $this;
    }

    public function getPurchaseShop(): ?string
    {
        return $this->purchaseShop;
    }

    public function setPurchaseShop(?string $purchaseShop): self
    {
        $this->purchaseShop = $purchaseShop;
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

    public function isActive()
    {
        return $this->getStatus() == self::STATUS_ACTIVE;
    }

    public function isCancelled()
    {
        return $this->getStatus() == self::STATUS_CANCELED;
    }

    public function isDeleted()
    {
        return $this->getStatus() == self::STATUS_DELETED;
    }

    /**
     * Если есть price - возвращает price, иначе возвращает userPrice
     */
    public function getFinalPrice()
    {
        return $this->getPrice() ?: $this->getUserPrice();
    }

    /**
     * Если есть purchasePrice - возвращает purchasePrice, иначе возвращает finalPrice
     */
    public function getFinalPurchasePrice()
    {
        return $this->getPurchasePrice() ?: $this->getFinalPrice();
    }

    /**
     * Итоговая стоимость одного товара в $ = кол-во товара * цену товара
     */
    public function getTotal()
    {
        return $this->getAmount() * $this->getFinalPrice();
    }

    /**
     * Итоговая выкупная стоимость одного товара в $
     */
    public function getPurchaseTotal()
    {
        return $this->getAmount() * $this->getFinalPurchasePrice();
    }

    /**
     * Итоговая стоимость одного товара в руб. = кол-во товара * цену товара * курс $
     */
    public function getTotalRub()
    {
        if (!$this->getOrder()->getRate()) {
            return 0;
        }
        return ceil($this->getAmount() * $this->getFinalPrice() * $this->getOrder()->getRate());
    }

    /**
     * Итоговая выкупная стоимость одного товара в руб.
     */
    public function getPurchaseTotalRub()
    {
        if (!$this->getOrder()->getRate()) {
            return 0;
        }
        return $this->getAmount() * $this->getFinalPurchasePrice() * $this->getOrder()->getRate();
    }
}
