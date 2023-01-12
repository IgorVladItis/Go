<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Введите свой email',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'autofocus' => 'autofocus',
                    'placeholder' => 'Пожалуйста введите свой email'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Пожалуйста заполните поле'
                    ]),
                    new Email([
                        'message' => 'Пожалуйста введите правильный email'
                    ])
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'Я соглашаюсь с <a href="#">privecy policy</a> *',
                'required' => true,
                'label_html' => true,
                'mapped' => false,
                'attr' => [
                    'class' => 'custom-control-input'
                ],
                'label_attr' => [
                    'class' => 'custom-control-label'
                ],
                'constraints' => [
                    new IsTrue([
                        'message' => 'Pls check the box',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Придумайте пароль',
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control',
                    'autocomplete' => 'new-password'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Пожалуйста введите ваш пароль',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Ваш пароль должен быть не меньше {{ limit }} символов',
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
