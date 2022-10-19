<?php

namespace App\Form;

use App\Enums\RoleDictionary;
use App\Entity\Group;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
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
            ->add($builder->create('address', FormType::class)
                ->add('city', TextType::class)
                ->add('street', TextType::class)
                ->add('home')
            )
            ->add('avatar', FileType::class, [
                'label' => 'avatar',
                'required' => false,
                'constraints' => [
                    new Image(['maxSize' => '1024k'])
                ],
            ])
            ->add('roles', CollectionType::class, [
                'entry_type' => ChoiceType::class,
                'entry_options' => [
                    'label' => 'Роль',
                    'choices' => [
                        RoleDictionary::getRoles()
                    ],
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
