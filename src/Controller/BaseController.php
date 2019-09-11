<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\LogMovementService;
use App\Services\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

//TODO
// LogMovement - загрузка логов на нужных страницах

/**
 * Строка ниже нужна для автодополнения PhpShtorm'a,
 * т.к. метод parent::getUser() не знает, объект какого класса он вернет.
 * @method User getUser()
 */
abstract class BaseController extends AbstractController
{
    protected $em;
    protected $logger;
    protected $mailer;
    protected $logMovementService;

    public function __construct(
        EntityManagerInterface $em,
        LoggerInterface $logger,
        MailService $mailer,
        LogMovementService $logMovementService)
    {
        $this->em = $em;
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->logMovementService = $logMovementService;
    }

    protected function getEm(): EntityManagerInterface
    {
        return $this->em;
    }

    public static function getSubscribedServices()
    {
        return array_merge(parent::getSubscribedServices(), [
            LogMovementService::class => LogMovementService::class,
        ]);
    }
}
