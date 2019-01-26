<?php

namespace App\Security;

use App\Entity\User;
use App\Helpers\MailService;
use App\Helpers\TokenGenerator;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    private $logger;
    private $router;
    private $mailer;
    private $manager;
    private $userRepository;
    private $translator;
    private $tokenGenerator;
    private $csrfTokenManager;
    private $passwordEncoder;

    public function __construct(
        LoggerInterface $logger,
        RouterInterface $router,
        MailService $mailer,
        ObjectManager $manager,
        UserRepository $userRepository,
        TranslatorInterface $translator,
        TokenGenerator $tokenGenerator,
        CsrfTokenManagerInterface $csrfTokenManager,
        UserPasswordEncoderInterface $passwordEncoder
    )
    {
        $this->logger = $logger;
        $this->router = $router;
        $this->mailer = $mailer;
        $this->manager = $manager;
        $this->userRepository = $userRepository;
        $this->translator = $translator;
        $this->tokenGenerator = $tokenGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    // If we return false from supports(), nothing else happens
    // If we return true, Symfony will immediately call getCredentials()
    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === 'app_login'
            && $request->isMethod('POST');
    }

    // Our job in getCredentials() is simple: to read our authentication credentials off of the request and return them
    public function getCredentials(Request $request)
    {
        $credentials =  [
            'email'      => $request->request->get('email'),
            'password'   => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token')
        ];

        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }

    // Our job in getUser() is to use these $credentials to return a User object, or null if the user isn't found
    // If this returns null, the whole authentication process will stop, and the user will see an error
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        return $this->userRepository->findOneBy(['email' => $credentials['email']]);
    }

    // This is your opportunity to check to see if the user's password is correct, or any other last, security checks
    public function checkCredentials($credentials, UserInterface $user)
    {
        $result = $this->passwordEncoder->isPasswordValid($user, $credentials['password']);

        /** @var User $user */
        if ($user->isNotActivated()) {
            $user->setActivationToken($this->tokenGenerator->generateToken());
            $this->manager->persist($user);
            $this->manager->flush();
            $this->mailer->sendUserRegisteredWithActivationEmailMessage($user);
        }

        if (!$user->isActive()) {
            $messages = [
                User::INACTIVE_REASON_BANNED        => '~auth.user.banned',
                User::INACTIVE_REASON_NOT_ACTIVATED => '~auth.user.not_activated',
                'unknown_reason'                    => '~auth.user.unknown_reason'
            ];

            $message = isset($messages[$user->getInactiveReason()])
                ? $messages[$user->getInactiveReason()]
                : $messages['unknown_reason'];

            throw new CustomUserMessageAuthenticationException($this->translator->trans($message));
        }

        return $result;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // send the user back to the previous page
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->router->generate('app_homepage'));
    }

    protected function getLoginUrl()
    {
        return $this->router->generate('app_login');
    }
}
