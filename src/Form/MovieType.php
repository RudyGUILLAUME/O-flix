<?php

namespace App\Form;

use App\Entity\Genre;
use App\Entity\Movie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class MovieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('realeaseDate', DateType::class)
            ->add('duration', IntegerType::class)
            ->add('summary', TextareaType::class, [
                // nombres de lignes du champ texte
                'attr' => [
                    'rows' => 3,
                ]
            ])
            ->add('synopsis', TextareaType::class, [
                // nombres de lignes du champ texte
                'attr' => [
                    'rows' => 5,
                ]
            ])
            ->add('poster', UrlType::class, [
                'default_protocol' => 'https',
            ])
            // sera calculé à partir des reviews de chaque film (moyenne)
            // ->add('rating')
            ->add('type', ChoiceType::class, [
                'label' => 'Film ou série',
                'choices' => [
                    'Film' => "Film",
                    'Série' => "Série",
                ],
                // boutons radio
                'expanded' => true,
            ])
            // l'EntityType permet de récupérer des données dans la base
            // @see https://symfony.com/doc/5.4/reference/forms/types/entity.html
            ->add('genres', EntityType::class, [
                // l'entité concernée
                'class' => Genre::class,
                // la propriété de l'entité qui sert d'affichage
                // si on ne précise pas le choice_label, on aura "la fameuse erreur"
                // /!\ Object of class App\Entity\Genre could not be converted to string
                'choice_label' => 'name',
                // on veut un choix multiple (array)
                'multiple' => true,
                // on veut des checkboxes (1 élément HTML par choix)
                'expanded' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
        ]);
    }
}
