<?php

namespace App\Form;

use App\Entity\UserAddress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastName', TextType::class)
            ->add('firstName', TextType::class)
            ->add('middleName', TextType::class)
            ->add('country', TextType::class)
            ->add('postCode')
            ->add('region', TextType::class)
            ->add('city', TextType::class)
            ->add('street', TextType::class)
            ->add('house')
            ->add('building')
            ->add('flat')
            ->add('phone')
            ->add('email', EmailType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserAddress::class,
        ]);
    }
}
