<?php

namespace App\Form;

use App\Entity\Serie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class SerieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Title of the TV-Show',
            ])
            ->add('overview', TextareaType::class)
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Ended' => 'ended',
                    'Canceled' => 'canceled',
                    'Returning' => 'returning',
                ],
                'expanded' => true
            ])
            ->add('vote')
            ->add('popularity')
            ->add('genres', ChoiceType::class, [
                'choices' => [
                    'Drama' => 'drama',
                    'SF' => 'sf',
                    'Comedy' => 'comedy',
                    'Western' => 'western',
                ]
            ])
            ->add('firstAirDate', DateType::class)
            ->add('lastAirDate')
            ->add('backdrop', FileType::class, [
                'mapped' => false,
                'constraints' => [
                    new Image(
                        maxSize: '5M',
                        mimeTypes: ['image/jpeg', 'image/png'],
                        maxSizeMessage: '5Mo max!',
                    )
                ]
            ])
            ->add('poster', FileType::class, [
                'mapped' => false, // à la soumission, le champ restera null, il ne sera pas traité automatiquement
                'constraints' => [
                    new Image(
                        maxSize: '5M',
                        mimeTypes: ['image/jpeg', 'image/png'],
                        maxSizeMessage: '5Mo max!'), // Validator Constraint
                ]
            ])
            ->add('tmdbId')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Serie::class,
            'required' => false,
        ]);
    }
}
