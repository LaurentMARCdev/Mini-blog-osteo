<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class ArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('article')
            ->setEntityLabelInPlural('Articles')
            ->setPageTitle('index', 'Gestion des articles')
            ->setPageTitle('edit', 'Modifier l\'article')
            ->setPageTitle('new', 'Nouvel article')
            ->setDefaultSort(['datePublication' => 'DESC']);
    }
    public function configureFields(string $pageName): iterable
    {
        return [
        //Champs visibles pour tous
            TextField::new('titre')
                ->setLabel('Titre')
                ->setMaxLength(60),
            TextField::new('slug')
                ->setLabel('Slug (URL)')
                ->setHelp('Généré automatiquement si laissé vide. Attention à l\'unicité')
                ->hideOnIndex(),
        //Catégorie & Auteur:
            AssociationField::new('categorie')
                ->setLabel('Catégorie'),
            AssociationField::new('auteur')
                ->setLabel('Auteur')
                ->hideOnIndex(),
        //Contenu & extrait:
            TextareaField::new('extrait')
                ->setLabel('Extrait (résumé)')
                ->setHelp('Court texte affiché dans les listes publiques.')
                ->hideOnIndex(),
            TextEditorField::new('contenu')
                ->setLabel('Contenu de l\'article')
                ->setHelp('Supporte le HTML. Evite les scripts <script>.')
                ->hideOnIndex(),
            TextField::new('imagePrincipale')
                ->setLabel('Image principale (URL)')
                ->setHelp('URL complète de l\'image à la une')
                ->hideOnIndex(),
            // Statut de publication
            BooleanField::new('publie')
                ->setLabel('Publié'),
            // Dates
            DateTimeField::new('datePublication')
                ->setLabel('Date de publication')
                ->setFormat('dd/MM/yyyy'),
            DateTimeField::new('dateCreation')
                ->setLabel('Créé le')
                ->setFormat('dd/MM/yyyy HH:mm')
                ->hideOnForm()
                ->hideOnIndex(),
        ];
    }
    
}
