<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends BaseFixture
{
    public const ROLE_USER_REFERENCE  = 'role_user';
    public const ROLE_ADMIN_REFERENCE = 'role_admin';

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(1, self::ROLE_ADMIN_REFERENCE, function($i) {
            $user = new User();
            $user->setEmail('akim_now@mail.ru');
            $user->setName('Akim');
            $user->setRoles([User::ROLE_ADMIN, User::ROLE_MODERATOR]);
            $user->setLastLoginAt(new \DateTime('-1 day'));
            $user->setCreatedAt(new \DateTime('-10 day'));
            $user->setStatus(User::STATUS_ACTIVE);
            $user->clearInactiveReason();
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                'secret1S'
            ));

            return $user;
        });

        $this->createMany(10, self::ROLE_USER_REFERENCE, function($i) {
            $user = new User();
            $user->setEmail(sprintf('user%d@example.com', $i));
            $user->setName($this->faker->unique()->firstName);
            $user->setLastLoginAt(new \DateTime('-1 day'));
            $user->setCreatedAt(new \DateTime('-10 day'));
            $user->setStatus(User::STATUS_ACTIVE);
            $user->clearInactiveReason();
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                'secret1S'
            ));

            return $user;
        });

        $manager->flush();
    }
}
