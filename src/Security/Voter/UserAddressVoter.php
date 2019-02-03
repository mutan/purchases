<?php

namespace App\Security\Voter;

use App\Entity\UserAddress;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserAddressVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return \in_array($attribute, ['USER_ADDRESS_MANAGE'])
            && $subject instanceof UserAddress;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var UserAddress $subject */
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'USER_ADDRESS_MANAGE':
                // logic to determine if the user can EDIT
                // return true or false
                if ($subject->getUser() == $user && $subject->isActive()) {
                    return true;
                }
                break;
        }

        return false;
    }
}
