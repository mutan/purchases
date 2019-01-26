<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * После логина пользователя записываем время входа в БД и в лог
 */
class LoginSubscriber implements EventSubscriberInterface
{
    private $logger;
    private $manager;

    public function __construct (LoggerInterface $logger, ObjectManager $manager)
    {
        $this->logger = $logger;
        $this->manager = $manager;
    }

    /**
     * 1. Записываем дату и время входа last_login_at
     * 2. Записываем факт входа в лог
     *
     * @param InteractiveLoginEvent $event
     */
    public function onInteractiveLogin (InteractiveLoginEvent $event)
    {
        /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();
        $user->setLastLoginAt(new \DateTime());

        $this->manager->persist($user);
        $this->manager->flush();

        $this->logger->info('User logged in using login/password', [
            'user_id'     => $user->getUsername(),
            'remember_me' => (bool) $event->getRequest()->request->get('_remember_me')
        ]);
    }

    public static function getSubscribedEvents ()
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin',
        ];
    }
}