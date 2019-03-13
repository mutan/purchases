<?php

namespace App\Form;

use App\Entity\Basket;
use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class BasketUserData
{
    /**
     * @Assert\NotBlank(message="~not_blank")
     */
    private $manager;

    /**
     * @Assert\NotBlank(message="~not_blank")
     * @Assert\Url()
     */
    private $shop;

    /**
     * @Assert\Length(min=2, max=250, minMessage="~min", maxMessage="~max")
     */
    private $userComment;

    public function __construct(Basket $basket = null)
    {
        if ($basket) $this->extract($basket);
    }

    public function fill(Basket $basket)
    {
        $basket->setManager($this->getManager());
        $basket->setShop($this->getShop());
        $basket->setUserComment($this->getUserComment());
    }

    public function extract(Basket $basket)
    {
        $this->setManager($basket->getManager());
        $this->setShop($basket->getShop());
        $this->setUserComment($basket->getUserComment());
    }

    public function getManager(): ?User
    {
        return $this->manager;
    }

    public function setManager(?User $manager): self
    {
        $this->manager = $manager;

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
}