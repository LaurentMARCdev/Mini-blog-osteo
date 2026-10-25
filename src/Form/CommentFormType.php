<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CommentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Classes CSS Tailwind pour le champ texte, cohérent avec le reste du site
        $textareaCss = 'w-full px-4 py-2 bg-parchment dark:bg-encre border border-sepia/30 rounded focus:outline-none focus:border-sepia focus:ring-1 focus:ring-sepia';

        $builder
            // On ne demande à l'utilisateur QUE le contenu de son commentaire.
            // Les autres champs (auteur, article, statut, dateCreation) sont
            // remplis automatiquement dans le contrôleur avant persistance.
            ->add('contenu', TextareaType::class, [
                'label' => 'Votre commentaire',
                'label_attr' => ['class' => 'block text-sm font-semibold mb-2'],
                'attr' => [ 
                    'class' => $textareaCss,
                    'rows' => 4,
                    'placeholder' => 'Partagez votre réaction, votre question ou votre expérience…',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Un commentaire ne peut pas être vide.',
                    ]),
                    new Length([
                        'min' => 10,
                        'max' => 2000,
                        'minMessage' => 'Votre commentaire doit faire au moins {{ limit }} caractères.',
                        'maxMessage' => 'Votre commentaire ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
