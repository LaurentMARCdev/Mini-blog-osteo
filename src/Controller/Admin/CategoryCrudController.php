<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;


class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('catégorie')
            ->setEntityLabelInPlural('Catégories')
            ->setPageTitle('index', 'Gestion des catégories')
            ->setDefaultSort(['nom' => 'ASC']);
    }
    public function configureFields(string $pageName): iterable
    {
        return [
        TextField::new('nom')
            ->setLabel('Nom de la catégorie'),
        
        TextField::new('slug')
            ->setLabel('Slug (URL)')
            ->setHelp('Laisser vide pour générer automatiquement.')
            ->hideOnIndex(),
        
        TextareaField::new('description')
            ->setLabel('Description')
            ->hideOnIndex(),
    ];
    }

}
