<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Validator\Constraints as Assert;

class ProductManagerData
{
    /**
     * @Assert\Regex(pattern="/^\d+(\.\d{1,2})?$/", message="~regexp.price")
     * @Assert\GreaterThan(value=0)
     */
    public $price;

    /**
     * @Assert\Type(type="integer")
     */
    public $expectedWeight;

    /**
     * @Assert\Type(type="integer")
     */
    public $weight;

    /**
     * @Assert\Regex(pattern="/^\d+(\.\d{1,2})?$/", message="~regexp.price")
     * @Assert\GreaterThan(value=0)
     */
    public $purchasePrice;

    /**
     * @Assert\Length(min=1, max=250, minMessage="~min", maxMessage="~max")
     */
    public $purchaseShop;

    public function __construct(Product $product = null)
    {
        if ($product) $this->extract($product);
    }

    public function fill(Product $product)
    {
        $product->setPrice($this->price);
        $product->setExpectedWeight($this->expectedWeight);
        $product->setWeight($this->weight);
        $product->setPurchasePrice($this->purchasePrice);
        $product->setPurchaseShop($this->purchaseShop);
    }

    public function extract(Product $product)
    {
        $this->price = $product->getPrice();
        $this->expectedWeight = $product->getExpectedWeight();
        $this->weight = $product->getWeight();
        $this->purchasePrice = $product->getPurchasePrice();
        $this->purchaseShop = $product->getPurchaseShop();
    }
}
