<?php

namespace App\Form;

use App\Entity\Basket;
use Symfony\Component\Validator\Constraints as Assert;

class BasketManagerData
{
    /**
     * @Assert\Regex(pattern="/^\d+(\.\d{1,2})?$/", message="~regexp.price")
     * @Assert\GreaterThan(value=0)
     */
    public $deliveryToStock;

    /**
     * @Assert\Regex(pattern="/^\d+(\.\d{1,2})?$/", message="~regexp.price")
     * @Assert\GreaterThan(value=0)
     */
    public $deliveryToRussiaPerKg;

    /**
     * @Assert\Regex(pattern="/^\d+(\.\d{1,2})?$/", message="~regexp.price")
     * @Assert\GreaterThan(value=0)
     */
    public $deliveryToClient;

    /**
     * @Assert\Regex(pattern="/^\d+(\.\d{1,2})?$/", message="~regexp.price")
     * @Assert\GreaterThan(value=0)
     */
    public $additionalCost;

    public $additionalCostComment;

    /**
     * @Assert\Regex(pattern="/^\d+(\.\d{1,2})?$/", message="~regexp.price")
     * @Assert\GreaterThan(value=0)
     */
    public $rate;

    public $isRateFinal = false;

    /**
     * @Assert\Length(min=2, max=250, minMessage="~min", maxMessage="~max")
     */
    public $tracking;

    public function __construct(Basket $basket = null)
    {
        if ($basket) $this->extract($basket);
    }

    public function fill(Basket $basket)
    {
        $basket->setDeliveryToStock($this->deliveryToStock);
        $basket->setDeliveryToRussiaPerKg($this->deliveryToRussiaPerKg);
        $basket->setDeliveryToClient($this->deliveryToClient);
        $basket->setAdditionalCost($this->additionalCost);
        $basket->setAdditionalCostComment($this->additionalCostComment);
        $basket->setRate($this->rate);
        $basket->setIsRateFinal($this->isRateFinal);
        $basket->setTracking($this->tracking);
    }

    public function extract(Basket $basket)
    {
        $this->deliveryToStock = $basket->getDeliveryToStock();
        $this->deliveryToRussiaPerKg = $basket->getDeliveryToRussiaPerKg();
        $this->deliveryToClient = $basket->getDeliveryToClient();
        $this->additionalCost = $basket->getAdditionalCost();
        $this->additionalCostComment = $basket->getAdditionalCostComment();
        $this->rate = $basket->getRate();
        $this->isRateFinal = $basket->getIsRateFinal();
        $this->tracking = $basket->getTracking();
    }
}
