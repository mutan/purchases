<?php

namespace App\Helpers;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MailService
{
    private $twig;
    private $mailer;
    private $router;
    private $logger;
    private $parameterBag;
    private $noReplyEmail;

    public function __construct(
        \Twig_Environment $twig,
        \Swift_Mailer $mailer,
        RouterInterface $router,
        LoggerInterface $logger,
        ParameterBagInterface $parameterBag,
        string $noReplyEmail
    )
    {
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->router = $router;
        $this->logger = $logger;
        $this->parameterBag = $parameterBag;
        $this->noReplyEmail = $noReplyEmail;
    }

    public function sendMessage($templateName, $context, $fromEmail, $toEmail)
    {
        $context  = $this->twig->mergeGlobals($context);
        $template = $this->twig->load($templateName);
        $subject  = $template->renderBlock('subject', $context);
        $textBody = $template->renderBlock('body_text', $context);
        $htmlBody = $template->renderBlock('body_html', $context);

        $message = (new \Swift_Message())
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($toEmail)
        ;

        if (!empty($htmlBody)) {
            $message->setBody($htmlBody, 'text/html')->addPart($textBody, 'text/plain');
        } else {
            $message->setBody($textBody);
        }

        $result = $this->mailer->send($message);

        $logContext = [
            'to'       => $toEmail,
            'message'  => $textBody,
            'template' => $templateName
        ];

        if ($result) {
            $this->logger->info('Email sent', $logContext);
        } else {
            $this->logger->error('Email error', $logContext);
        }

        return $result;
    }

    public function sendUserRegisteredWithActivationEmailMessage(User $user)
    {
        $url = $this->router->generate(
            'app_user_activate',
            ['token' => $user->getActivationToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $context = [
            'user' => $user,
            'activationUrl' => $url
        ];

        $this->sendMessage(
            'emails/user_registered_with_activation.html.twig',
            $context,
            $this->noReplyEmail,
            $user->getEmail()
        );
    }

    public function sendUserActivatedEmailMessage(User $user)
    {
        $this->sendMessage('emails/user_activated.html.twig', ['user' => $user], $this->noReplyEmail, $user->getEmail());
    }

    public function sendPasswordResetLinkEmailMessage(User $user)
    {
        $url = $this->router->generate(
            'app_reset_password',
            ['token' => $user->getResetToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $context = [
            'user' => $user,
            'resetPasswordUrl' => $url,
        ];

        $this->sendMessage(
            'emails/password_reset_link.html.twig',
            $context,
            $this->noReplyEmail,
            $user->getEmail()
        );
    }

    public function sendPasswordResetedEmailMessage(User $user)
    {
        $this->sendMessage('emails/password_reseted.html.twig', ['user' => $user], $this->noReplyEmail, $user->getEmail());
    }
}