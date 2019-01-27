<?php

namespace App\Controller;

use App\Entity\User;
use App\Helpers\MailService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Строка ниже нужна для автодополнения PhpShtorm'a, т.к. метод parent::getUser() не знает, объект какого класса он вернет.
 * @method User getUser()
 */
abstract class BaseController extends AbstractController
{
    protected $logger;
    protected $mailer;

    public function __construct(LoggerInterface $logger, MailService $mailer)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
    }
}
