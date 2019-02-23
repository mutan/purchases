<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Product
{
    use TimestampableTrait;

    const STATUS_ACTIVE   = 'active'; // готов к покупке
    const STATUS_DELETED  = 'deleted'; // удален по инициативе пользователя
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Basket", inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $basket;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="~not_blank")
     * @Assert\Length(min=1, max=250, minMessage="~min", maxMessage="~max")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=2000)
     * @Assert\NotBlank(message="~not_blank")
     * @Assert\Length(min=1, max=2000, minMessage="~min", maxMessage="~max")
     * @Assert\Url()
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(min=1, max=250, minMessage="~min", maxMessage="~max")
     *
     */
    private $article;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=2)
     * @Assert\NotBlank(message="~not_blank")
     * @Assert\GreaterThan(value=0)
     */
    private $userPrice;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=2, nullable=true)
     * @Assert\Type(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="~not_blank")
     * @Assert\Type(type="integer")
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(min=2, max=250, minMessage="~min", maxMessage="~max")
     */
    private $comment;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type(type="integer")
     */
    private $expectedWeight;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type(type="integer")
     */
    private $weight;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=2, nullable=true)
     * @Assert\Type(type="float")
     */
    private $purchasePrice;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(min=1, max=250, minMessage="~min", maxMessage="~max")
     */
    private $purchaseShop;

    /**
     * @ORM\Column(type="string", length=255, options={"default" = Product::STATUS_ACTIVE})
     */
    private $status = self::STATUS_ACTIVE;

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

    public function setPrice(float $price): self
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
            throw new \InvalidArgumentException("Invalid product status");
        }

        $this->status = $status;

        return $this;
    }

    public function getBasket(): ?Basket
    {
        return $this->basket;
    }

    public function setBasket(?Basket $basket): self
    {
        $this->basket = $basket;

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

    public function __toString()
    {
        return $this->getIdWithPrefix();
    }

    public function getIdWithPrefix()
    {
        return 'PR' . $this->id;
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

    public function getSum()
    {
        $price = $this->getPrice() ?: $this->getUserPrice();

        return $this->getAmount() * $price;
    }

    public function getSumRub()
    {
        $price = $this->getPrice() ?: $this->getUserPrice();

        return ceil($this->getAmount() * $price * $this->getBasket()->getRate());
    }
}
