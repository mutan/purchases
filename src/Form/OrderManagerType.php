<?php

namespace App\Form;

use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class OrderManagerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('deliveryToStock', NumberType::class)
            ->add('deliveryToRussiaPerKg', NumberType::class)
            ->add('deliveryToClient', NumberType::class)
            ->add('additionalCost', NumberType::class)
            ->add('additionalCostComment',TextareaType::class)
            ->add('deliveryToStockReal', NumberType::class)
            ->add('deliveryToRussiaPerKgReal', NumberType::class)
            ->add('deliveryToClientReal', NumberType::class)
            ->add('additionalCostReal', NumberType::class)
            ->add('rate', NumberType::class)
            ->add('isRateFinal', CheckboxType::class)
            ->add('tracking', TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Order::class
        ]);
    }
}
