<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('url')
            ->add('article')
            ->add('userPrice')
            ->add('price')
            ->add('amount')
            ->add('comment')
            ->add('status')
            ->add('expectedWeight')
            ->add('weight')
            ->add('purchasePrice')
            ->add('purchaseShop')
            ->add('createDate')
            ->add('updateDate')
            ->add('user')
            ->add('basket')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
