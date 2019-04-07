<?php

namespace App\Helpers\Entiry;

use App\Entity\Basket;

class BasketHelper
{
    const STATUSES = [
        Basket::STATUS_NEW => [
            'label' => 'Новый',
            'description' => 'Заказы со статусом Новый можно редактировать и удалять',
            'next_allowed_statuses' => [Basket::STATUS_APPROVED, Basket::STATUS_DELETED],
        ],
        Basket::STATUS_APPROVED => 'Утвержден',
        Basket::STATUS_REDEEMED => 'Выкупается',
        Basket::STATUS_BOUGHT => 'Выкуплен',
        Basket::STATUS_SENT => 'Отправлен',
        Basket::STATUS_RECEIVED => '',
        Basket::STATUS_CANCELED => 'Отменен',
        Basket::STATUS_DELETED => 'Удален',
    ];
}
