<?php

namespace App\DataFixtures;

use App\Entity\Basket;
use App\Entity\Product;
use App\Entity\User;
use App\Entity\UserAddress;
use App\Entity\UserPassport;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends BaseFixture
{
    public const ROLE_USER_REFERENCE    = 'role_user';
    public const ROLE_MANAGER_REFERENCE = 'role_manager';
    public const ROLE_ADMIN_REFERENCE   = 'role_admin';

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
            $user->setRoles([User::ROLE_ADMIN, User::ROLE_MANAGER]);
            $user->setCreateDate(new \DateTime('-10 day'));
            $user->setStatus(User::STATUS_ACTIVE);
            $user->clearInactiveReason();
            $user->setPassword($this->passwordEncoder->encodePassword($user, 'secret1S'));

            $userAddress = $this->createAddress();
            $this->manager->persist($userAddress);
            $user->addUserAddress($userAddress);

            $userPassport = $this->createPassport();
            $this->manager->persist($userPassport);
            $user->addUserPassport($userPassport);

            return $user;
        });

        $this->createMany(1, self::ROLE_MANAGER_REFERENCE, function($i) {
            $user = new User();
            $user->setEmail('gamerxxx@mail.ru');
            $user->setName('Яша');
            $user->setRoles([User::ROLE_MANAGER]);
            $user->setCreateDate(new \DateTime('-10 day'));
            $user->setStatus(User::STATUS_ACTIVE);
            $user->clearInactiveReason();
            $user->setPassword($this->passwordEncoder->encodePassword($user, 'secret1S'));

            $userAddress = $this->createAddress();
            $this->manager->persist($userAddress);
            $user->addUserAddress($userAddress);

            $userPassport = $this->createPassport();
            $this->manager->persist($userPassport);
            $user->addUserPassport($userPassport);

            return $user;
        });

        $this->createMany(10, self::ROLE_USER_REFERENCE, function($i) {
            $user = new User();
            $user->setEmail(sprintf('user%d@example.com', $i));
            $user->setName($this->faker->unique()->firstName);
            $user->setCreateDate(new \DateTime('-10 day'));
            $user->setStatus(User::STATUS_ACTIVE);
            $user->clearInactiveReason();
            $user->setPassword($this->passwordEncoder->encodePassword($user, 'secret1S'));

            $userAddress = $this->createAddress();
            $this->manager->persist($userAddress);
            $user->addUserAddress($userAddress);

            $userPassport = $this->createPassport();
            $this->manager->persist($userPassport);
            $user->addUserPassport($userPassport);

            return $user;
        });

        $manager->flush();

        $this->createMany(10, 'basket', function($i) {
            $admin = $this->getRandomReference(self::ROLE_ADMIN_REFERENCE);
            $manager = $this->getRandomReference(self::ROLE_MANAGER_REFERENCE);
            $user = $this->getRandomReference(self::ROLE_USER_REFERENCE);
            $basket = $this->createBasket($this->faker->randomElement([$admin, $manager]));
            $product = $this->createProduct();
            $this->manager->persist($product);
            $user->addProduct($product);
            $basket->addProduct($product);
            $this->manager->persist($basket);
            $user->addBasket($basket);

            return $basket;
        });

        $manager->flush();
    }

    protected function createAddress(): UserAddress
    {
        $userAddress = new UserAddress();
        $userAddress->setLastName($this->faker->lastName)
                    ->setFirstName($this->faker->firstName)
                    ->setMiddleName($this->faker->colorName)
                    ->setCountry($this->faker->country)
                    ->setPostCode($this->faker->postcode)
                    ->setRegion($this->faker->state)
                    ->setCity($this->faker->city)
                    ->setStreet($this->faker->streetName)
                    ->setHouse($this->faker->numberBetween(1, 100))
                    ->setFlat($this->faker->numberBetween(1, 100))
                    ->setPhone($this->faker->phoneNumber)
                    ->setEmail($this->faker->email);

        if ($this->faker->boolean(20)) $userAddress->setBuilding($this->faker->numberBetween(1, 3));

        return $userAddress;
    }

    protected function createPassport(): UserPassport
    {
        $userPassport = new UserPassport();
        $userPassport->setSeries($this->faker->numberBetween(1000, 9999))
                     ->setNumber($this->faker->numberBetween(100000, 999999))
                     ->setGiveBy($this->faker->sentence)
                     ->setGiveDate($this->faker->dateTime())
                     ->setBirthDate($this->faker->dateTime())
                     ->setInn($this->faker->randomNumber(6) . $this->faker->randomNumber(6));

        return $userPassport;
    }

    protected function createBasket($manager): Basket
    {
        $basket = new Basket();
        $basket->setShop('https://www.coolstuffinc.com')
               ->setManager($manager)
               ->setUserComment($this->faker->text);

        return $basket;
    }

    protected function createProduct(): Product
    {
        $product = new Product();
        $product->setName('Faithless Looting 128/254')
                ->setUrl('https://beta.trollandtoad.com/magic-the-gathering/ultimate-masters-singles/faithless-looting')
                ->setAmount(1)
                ->setUserPrice(0.49);

        return $product;
    }
}
