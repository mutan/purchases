<?php

namespace App\Security\Voter;

use App\Entity\UserPassport;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class UserPassportVoter extends Voter
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        // If the attribute isn't one we support, return false
        // Only vote on UserPassport objects inside this voter
        return in_array($attribute, [
                'USER_PASSPORT_EDIT', // by user
            ])
            && $subject instanceof UserPassport;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        // The user must be logged in; if not, deny access
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ROLE_ADMIN can do anything
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // You know $subject is a UserPassport object, thanks to supports
        // Check conditions and return true to grant permission
        /** @var UserPassport $subject */
        switch ($attribute) {
            case 'USER_PASSPORT_EDIT':
                if ($subject->getUser() == $user && $subject->isNew()) {
                    return true;
                }
                break;
        }

        return false;
    }
}
