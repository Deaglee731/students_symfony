<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Name',
                'required' => true,
            ])
            ->add('first_name', null, [
                'label' => 'First name',
                'required' => true,
            ])
            ->add('last_name', null, [
                'label' => 'Last name',
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => 'email',
                'required' => true,
            ])
            ->add('birthday', DateType::class, [
                'label' => 'birthday',
                'required' => true,
            ])
            ->add('groups', EntityType::class, [
                'class' => Group::class,
                'choice_label' => 'name',
                'required' => true,
            ])
            ->add('address', J)
            ->add('avatar', FileType::class, [
                'label' => 'avatar',
                'required' => false,
                'constraints' => [
                    new Image(['maxSize' => '1024k'])
                ],
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
