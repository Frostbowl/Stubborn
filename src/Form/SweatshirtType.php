<?php

namespace App\Form;

use App\Entity\Sweatshirt;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class SweatshirtType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix',
                'scale' => 2, // 2 décimales pour les prix
            ])
            ->add('stockXS', NumberType::class, [
                'label' => 'Stock XS',
            ])
            ->add('stockS', NumberType::class, [
                'label' => 'Stock S',
            ])
            ->add('stockM', NumberType::class, [
                'label' => 'Stock M',
            ])
            ->add('stockL', NumberType::class, [
                'label' => 'Stock L',
            ])
            ->add('stockXL', NumberType::class, [
                'label' => 'Stock XL',
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Image du sweatshirt',
                'required'=> true,
                'mapped'=> false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sweatshirt::class,
        ]);
    }
}
