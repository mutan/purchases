<?php

namespace App\Services\Entity;

use App\Entity\Order;

class OrderHelper
{
    // TODO Ужас ужас переделать
    const STATUSES = [
        Order::STATUS_NEW => [
            'label' => 'Новый',
            'description' => 'Заказы со статусом Новый можно редактировать и удалять',
            'next_allowed_statuses' => [Order::STATUS_APPROVED, Order::STATUS_DELETED],
        ],
        Order::STATUS_APPROVED => [
            'label' => 'Утвержден',
            'description' => 'Заказы со статусом Утвержден больше нельзя редактировать или удалять, но можно вернуть обратно в статус Новый.',
            'next_allowed_statuses' => [Order::STATUS_NEW, Order::STATUS_REDEEMED],
        ],
        Order::STATUS_REDEEMED => [
            'label' => 'Выкупается',
            'description' => 'Менеджер начал выкупать ваш заказ. Редактировать или удалять заказ, а также товары в нем, больше нельзя.',
            'next_allowed_statuses' => [Order::STATUS_BOUGHT],
        ],
        Order::STATUS_BOUGHT => 'Выкуплен',
        Order::STATUS_SENT => 'Отправлен',
        Order::STATUS_RECEIVED => 'Получен',
        Order::STATUS_CANCELED => 'Отменен',
        Order::STATUS_DELETED => 'Удален',
    ];
}
