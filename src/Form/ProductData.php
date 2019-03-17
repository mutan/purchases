<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Validator\Constraints as Assert;

class ProductData
{
    /**
     * @Assert\NotBlank(message="~not_blank")
     * @Assert\Length(min=1, max=250, minMessage="~min", maxMessage="~max")
     */
    public $name;

    /**
     * @Assert\NotBlank(message="~not_blank")
     * @Assert\Length(min=1, max=2000, minMessage="~min", maxMessage="~max")
     * @Assert\Url()
     */
    public $url;

    /**
     * @var int
     * @Assert\NotBlank(message="~not_blank")
     * @Assert\Type(type="integer")
     */
    public $amount;

    /**
     * @Assert\NotBlank(message="~not_blank")
     * @Assert\Regex(pattern="/^\d+(\.\d{1,2})?$/", message="~regexp.price")
     * @Assert\GreaterThan(value=0)
     */
    public $userPrice;

    /**
     * @Assert\Length(min=1, max=250, minMessage="~min", maxMessage="~max")
     *
     */
    public $article;

    /**
     * @Assert\Length(min=2, max=250, minMessage="~min", maxMessage="~max")
     */
    public $comment;

    public function __construct(Product $product = null)
    {
        if ($product) $this->extract($product);
    }

    public function fill(Product $product)
    {
        $product->setName($this->name);
        $product->setUrl($this->url);
        $product->setAmount($this->amount);
        $product->setUserPrice($this->userPrice);
        $product->setArticle($this->article);
        $product->setComment($this->comment);
    }

    public function extract(Product $product)
    {
        $this->name = $product->getName();
        $this->url = $product->getUrl();
        $this->amount = (int) $product->getAmount();
        $this->userPrice = $product->getUserPrice();
        $this->article = $product->getArticle();
        $this->comment = $product->getComment();
    }
}
