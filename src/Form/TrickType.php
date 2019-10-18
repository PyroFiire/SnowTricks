<?php

namespace App\Form;

use App\Entity\Trick;
use App\Form\MediaType;
use App\Form\PictureType;
use App\Entity\GroupTrick;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('videos', CollectionType::class, [
                'entry_type' => VideoType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
                'mapped' => false,
                'prototype' => true,
                'attr' => [
                    'class' => 'col-12'
                ]
            ])
            ->add('pictures', CollectionType::class, [
                'entry_type' => PictureType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
                'mapped' => false,
                'prototype' => true,
            ])
            ->add('title')
            ->add('description')
            ->add('groupTrick', EntityType::class, [
                'class' => GroupTrick::class,
                'choice_label' => 'name'
            ])
            ->add('spotlightPicturePath', PictureType::class, [
                'data_class' => null,
                'mapped' => false,
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
