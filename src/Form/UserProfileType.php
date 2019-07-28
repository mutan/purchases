<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('vk', TextType::class, [
                'help' => 'В Telegram перейдите в настройки, выберите "Изменить профиль", затем "Имя пользовителя", там нажмите на ссылку, и она скопируется в буфер обмена',
            ])
            ->add('telegram', TextType::class, [
                'help' => 'В меню Вконтакте выберите "Моя страница" и скопируйте ссылку из адресной строки браузера',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
