<?php

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // on peut laisser à Symfony le choix de deviner le type de champ
            // pour cela on laisse l'argument 2 à null
            // ce qui permet de configurer les options (argument 3)
            // => ici on aura un TextType
            ->add('username', null, [
                'label' => 'Pseudo',
                'help' => 'Ceci est un message d\'aide',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Courriel',
                // si besoin de placeholder sur un champ autre que ChoiceType
                'attr' => [
                    'placeholder' => 'ex. toto@toto.com',
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Critique',
            ])
            ->add('rating', ChoiceType::class, [
                'label' => 'Avis',
                'choices' => [
                    'Excellent' => 5,
                    'Très bon' => 4,
                    'Bon' => 3,
                    'Peut mieux faire' => 2,
                    'A éviter' => 1,
                ],
                // @see https://symfony.com/doc/current/reference/forms/types/choice.html#placeholder
                'placeholder' => 'Votre choix...'
            ])
            ->add('reactions', ChoiceType::class, [
                'label' => 'Ce film vous a fait',
                'choices' => [
                    'Rire' => 'smile',
                    'Pleurer' => 'cry',
                    'Réfléchir' => 'think',
                    'Dormir' => 'sleep',
                    'Rêver' => 'dream',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('watchedAt', DateType::class, [
                'label' => 'Vous avez vu ce film le',
                // la propriété $watchedAt de notre entité est de type DateTimeImmutable
                'input' => 'datetime_immutable',
                //'widget' => 'single_text'
                // gestion des années
                // @see https://symfony.com/doc/current/reference/forms/types/date.html#years
                // @todo date début = $releaseDate du film
                'years' => range(date('Y'), 1950),
            ])
            // on ne souhaite pas demander à l'utilisateur de choisir le film
            // puisqu'on l'a déjà dans l'URL (via le ParamConverter)
            // ->add('movie')
        ;
    }

    /**
     * ceci configure les options du formulaire
     * le même type d'options que pour les champs de form.
     * @see https://symfony.com/doc/current/reference/forms/types/form.html
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
            // on pourrait mettre ici le novalidate
            // 'attr' => [
            //     'novalidate' => 'novalidate',
            // ]
        ]);
    }
}
