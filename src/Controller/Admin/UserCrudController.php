<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('compte')
            ->setEntityLabelInPlural('Comptes')
            ->setPageTitle('index', 'Gestion des comptes utilisateurs')
            ->setDefaultSort(['email' => 'ASC']);
    }
    public function configureFields(string $pageName): iterable
    {
        return [
        EmailField::new('email')
            ->setLabel('Adresse email'),
        
        ChoiceField::new('roles')
            ->setLabel('Rôles')
            ->setChoices([
                'Utilisateur' => 'ROLE_USER',
                'Administrateur' => 'ROLE_ADMIN',
            ])
            ->allowMultipleChoices()
            ->renderAsBadges([
                'ROLE_USER' => 'info',
                'ROLE_ADMIN' => 'warning',
            ]),

        // Mot de passe uniquement visible en création
        // PAS de hashage ici, juste l'affichage du champ.
        // Pour un vrai hashage lors d'une création d'utilisateur depuis l'admin,
        // il faudrait un EventListener Doctrine ou une méthode dans UserCrudController.
        // Pour le périmètre du blog, l'admin ne crée pas d'utilisateurs côté back
        // (ils s'inscrivent eux-mêmes via /register), donc on garde ça simple 
        // pour cette version du blog. à garder comme développement futur du blog. 
    ];
    }

}
