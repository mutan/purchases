<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\LogMovementService;
use App\Services\MailService;
use Psr\Log\LoggerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Строка ниже нужна для автодополнения PhpShtorm'a,
 * т.к. метод parent::getUser() не знает, объект какого класса он вернет.
 * @method User getUser()
 */
abstract class BaseController extends AbstractController
{
    protected $logger;
    protected $mailer;
    protected $logMovementService;

    public function __construct(LoggerInterface $logger, MailService $mailer, LogMovementService $logMovementService)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->logMovementService = $logMovementService;
    }

    protected function getEm(): ObjectManager
    {
        return $this->getDoctrine()->getManager();
    }

    protected function getLogMovementService(): LogMovementService
    {
        return $this->logMovementService;
    }
}
