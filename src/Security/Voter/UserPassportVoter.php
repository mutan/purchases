<?php

namespace App\Security\Voter;

use App\Entity\UserPassport;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserPassportVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        return \in_array($attribute, ['USER_PASSPORT_MANAGE'])
            && $subject instanceof UserPassport;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var UserPassport $subject */
        switch ($attribute) {
            case 'USER_PASSPORT_MANAGE':
                if ($subject->getUser() == $user && $subject->isNew()) return true;
                break;
        }

        return false;
    }
}
