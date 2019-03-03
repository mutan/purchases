<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Basket;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
            ->add('deliveryToStock', NumberType::class)
            ->add('deliveryToRussia', NumberType::class)
            ->add('deliveryToClient', NumberType::class)
            ->add('additionalCost', NumberType::class)
            ->add('additionalCostComment',TextareaType::class)
            ->add('rate', NumberType::class)
            ->add('isRateFinal', CheckboxType::class)
            ->add('tracking', TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Basket::class,
        ]);
    }
}
