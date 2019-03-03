<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Basket;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BasketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('shop',TextType::class, ['attr' => ['autofocus' => true]])
            ->add('manager', EntityType::class, [
                'class'       => User::class,
                'query_builder' => function (UserRepository $userRepository) {
                    return $userRepository
                        ->createQueryBuilder('u')
                        ->andWhere('u.roles LIKE :role')
                        ->setParameter('role', '%' . User::ROLE_MANAGER . '%');
                },
                'placeholder' => ''
            ])
            ->add('userComment',TextareaType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Basket::class,
        ]);
    }
}
