<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormTypeInterface;

class EditProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Title',
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Assert\NotBlank([], 'Заполните поле ')
                ]
            ])
            ->add('price', NumberType::class, [
                'label' => 'Price',
                'scale' => 2,
                'html5' => true,
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0
                ]
            ])
            ->add('quantity', Type\IntegerType::class, [
                'label' => 'Quantity',
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('description', Type\TextareaType::class, [
                'label' => 'Description',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'overflow: hidden;'
                ]
            ])
            ->add('isPublished', Type\CheckboxType::class, [
                'label' => 'Is published',
                'required' => false,
                'attr' => [
                    'class' => 'form-control-input'
                ],
                'label_attr' => [
                    'class' => 'form-check-label'
                ]
            ])
            ->add('isDeleted', Type\CheckboxType::class, [
                'label' => 'Is deleted',
                'required' => false,
                'attr' => [
                    'class' => 'form-control-input'
                ],
                'label_attr' => [
                    'class' => 'form-check-label'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
