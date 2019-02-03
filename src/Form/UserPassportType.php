<?php

namespace App\Form;

use App\Entity\UserPassport;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserPassportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('series',TextType::class, ['help' => '4 цифры'])
            ->add('number',TextType::class, ['help' => '6 цифр'])
            ->add('giveBy',TextType::class)
            ->add('giveDate',DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'html5' => false, // prevents rendering it as type="date", to avoid HTML5 date pickers
                'help' => 'В формате дд-мм-гггг',
            ])
            ->add('birthDate',DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'html5' => false,
                'help' => 'В формате дд-мм-гггг'
            ])
            ->add('inn',TextType::class, ['help' => '12 цифр'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserPassport::class,
        ]);
    }
}
