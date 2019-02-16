<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class, ['attr' => ['autofocus' => true]])
            ->add('url', TextType::class)
            ->add('article', TextType::class)
            ->add('userPrice', NumberType::class)
            ->add('price', TextType::class)
            ->add('amount', TextType::class)
            ->add('comment', TextareaType::class)
            ->add('expectedWeight', TextType::class)
            ->add('purchasePrice', TextType::class)
            ->add('purchaseShop', TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
