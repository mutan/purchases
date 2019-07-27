<?php

namespace App\Security\Voter;

use App\Entity\UserAddress;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class UserAddressVoter extends Voter
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        // If the attribute isn't one we support, return false
        // Only vote on UserAddress objects inside this voter
        return in_array($attribute, [
                'USER_ADDRESS_EDIT', // by user
            ])
            && $subject instanceof UserAddress;
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
        /** @var UserAddress $subject */
        switch ($attribute) {
            case 'USER_ADDRESS_EDIT':
                if ($subject->getUser() == $user && $subject->isNew()) {
                    return true;
                }
                break;
        }

        return false;
    }
}
