<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class, [
                'help' => 'Как в магазине.',
                'attr' => ['autofocus' => true],
            ])
            ->add('url', TextType::class, ['help' => 'Именно на этот товар в магазине. Должен начинаться с http.'])
            ->add('article', TextType::class, ['help' => 'Уникальный номер товара, ebayId и т.п., если есть.'])
            ->add('userPrice', TextType::class, ['help' => 'Цена как магазине. Разделитель целой и десятичной части – точка.'])
            ->add('price', TextType::class)
            ->add('amount', TextType::class, ['help' => 'Количество, шт.'])
            ->add('comment', TextareaType::class, ['help' => 'Например, характеристики товара, которые не определяются по ссылке и их надо выбирать отдельно на странице товара в магазине.'])
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
