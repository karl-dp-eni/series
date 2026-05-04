<?php

namespace App\Form;

use App\Entity\Season;
use App\Entity\Serie;
use App\Repository\SerieRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeasonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('number')
            ->add('firstAirDate')
            ->add('overview')
            ->add('poster')
            ->add('tmdbId')
            ->add('serie', EntityType::class, [ // Permet de générer un select avec toutes les séries en BDD
                'class' => Serie::class,
                'choice_label' => 'name', // Plutôt que d'afficher l'id, on affiche le nom de la série
                // Par défaut findAll s'exécute et récupère tout dans aucun ordre particulier.
                // Pour choisir quoi afficher dans le select, on fait une fonction anonyme avec le query_builder
                'query_builder' => function (SerieRepository $serieRepository) {
                    // Permet d'afficher l'ensemble des séries par ordre alphabétique
                    return $serieRepository->createQueryBuilder('s')->addOrderBy('s.name'); // addOrderBy plus performant qu'orderBy
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Season::class,
            'required' => false,
        ]);
    }
}
