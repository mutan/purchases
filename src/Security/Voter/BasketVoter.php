<?php

namespace App\Security\Voter;

use App\Entity\Basket;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class BasketVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        return \in_array($attribute, [
                'BASKET_OPERATE', //by user
                'BASKET_MANAGE' //by manager
            ])
            && $subject instanceof Basket;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Basket $subject */
        switch ($attribute) {
            case 'BASKET_OPERATE':
                if ($subject->getUser() == $user && $subject->isNew()) {
                    return true;
                }
                break;
            case 'BASKET_MANAGE':
                if ($subject->getManager() == $user) {
                    return true;
                }
                break;
        }

        return false;
    }
}
