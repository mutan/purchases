<?php

namespace App\Resources;

use App\Entity\Order;

class OrderHelper
{
    const STATUSES = [
        Order::STATUS_NEW => [
            'label' => 'Новый',
            'description' => 'Заказ и товары в нем можно редактировать и удалять',
            'next_allowed_statuses' => [Order::STATUS_APPROVED, Order::STATUS_DELETED],
        ],
        Order::STATUS_APPROVED => [
            'label' => 'Утвержден',
            'description' => 'Заказ и товары в нем больше нельзя редактировать или удалять, но можно вернуть обратно в статус Новый',
            'next_allowed_statuses' => [Order::STATUS_NEW, Order::STATUS_REDEEMED],
        ],
        Order::STATUS_REDEEMED => [
            'label' => 'Выкупается',
            'description' => 'Менеджер начал выкупать ваш заказ. Редактировать или удалять заказ и товары в нем больше нельзя',
            'next_allowed_statuses' => [Order::STATUS_BOUGHT],
        ],
        Order::STATUS_BOUGHT => [
            'label' => 'Выкуплен',
            'description' => 'Заказ выкуплен менеджером. Это самый долгий статус',
            'next_allowed_statuses' => [Order::STATUS_SENT],
        ],
        Order::STATUS_SENT => [
            'label' => 'Отправлен',
            'description' => 'Заказ отправлен вам',
            'next_allowed_statuses' => [Order::STATUS_RECEIVED],
        ],
        Order::STATUS_RECEIVED => [
            'label' => 'Получен',
            'description' => 'Вы получили заказ',
            'next_allowed_statuses' => [],
        ],
        Order::STATUS_CANCELED => [
            'label' => 'Отменен',
            'description' => 'Заказ отменен клиентом, виден клиенту',
            'next_allowed_statuses' => [],
        ],
        Order::STATUS_DELETED => [
            'label' => 'Удален',
            'description' => 'Заказ удален по инициативе клиента, не виден клиенту',
            'next_allowed_statuses' => [],
        ],
    ];
}
