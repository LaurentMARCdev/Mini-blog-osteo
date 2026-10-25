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

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
{
    // Classes CSS Tailwind communes aux inputs — on les définit une fois ici
    // pour les réutiliser et garder le code lisible
    $inputCss = 'w-full px-4 py-2 bg-parchment dark:bg-encre border border-sepia/30 rounded focus:outline-none focus:border-sepia focus:ring-1 focus:ring-sepia';

    $builder
        // ═══ Email ═══
        ->add('email', EmailType::class, [
            'label' => 'Adresse email',
            'label_attr' => ['class' => 'block text-sm font-semibold mb-2'],
            'attr' => [
                'class' => $inputCss,
                'placeholder' => 'vous@exemple.fr',
                'autocomplete' => 'email',
            ],
        ])

        // ═══ Mot de passe ═══
        ->add('plainPassword', PasswordType::class, [
            // 'mapped' => false : ce champ N'EST PAS lié à une propriété de User
            // (on hashe le mdp manuellement dans le contrôleur avant persistance)
            'mapped' => false,
            'label' => 'Mot de passe',
            'label_attr' => ['class' => 'block text-sm font-semibold mb-2'],
            'attr' => [
                'class' => $inputCss,
                'autocomplete' => 'new-password',
                'placeholder' => 'Au moins 6 caractères',
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Merci de choisir un mot de passe.',
                ]),
                new Length([
                    'min' => 6,
                    'minMessage' => 'Le mot de passe doit faire au moins {{ limit }} caractères.',
                    'max' => 4096, // limite imposée par Symfony pour éviter les DoS
                ]),
            ],
        ])

        // ═══ Case à cocher CGV ═══
        ->add('agreeTerms', CheckboxType::class, [
            'mapped' => false,
            'label' => 'J\'accepte les conditions d\'utilisation du blog.',
            'label_attr' => ['class' => 'text-sm cursor-pointer'],
            'attr' => [
                'class' => 'mr-2',
            ],
            // 'row_attr' habille le <div> qui contient la checkbox + son label
            'row_attr' => ['class' => 'flex items-center gap-2 pt-2'],
            'constraints' => [
                new IsTrue([
                    'message' => 'Vous devez accepter les conditions d\'utilisation.',
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
