<?php

namespace App\Security\Voter;

use App\Entity\Product;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ProductVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        return \in_array($attribute, ['PRODUCT_MANAGE'])
            && $subject instanceof Product;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Product $subject */
        switch ($attribute) {
            case 'PRODUCT_MANAGE':
                if ($subject->getUser() == $user && $subject->isActive()) {
                    return true;
                }
                break;
        }

        return false;
    }
}
