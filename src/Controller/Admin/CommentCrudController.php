<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;

class CommentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Comment::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('commentaire')  // utilisé par ex dans "Add commentaire"
            ->setEntityLabelInPlural('Commentaires')   // utilisé en titre de la liste
            ->setPageTitle('index', 'Modération des commentaires')
            ->setPageTitle('edit', 'Modérer le commentaire')
            ->setPageTitle('new', 'Ajouter un commentaire')
            ->setDefaultSort(['dateCreation' => 'DESC']); // les plus récents en haut
    }
    //Ajout des deux boutons et des actions avec les bonnes conditions associées
    public function configureActions(Actions $actions): Actions
    {
        // Création des deux actions custom
        $valider = Action::new('valider', '✅ Valider', 'fa fa-check')
            ->linkToCrudAction('validerCommentaire')
            ->setCssClass('btn btn-success btn-sm')
            // Affichée UNIQUEMENT si le commentaire est en attente
            ->displayIf(static function (Comment $commentaire): bool {
                return $commentaire->getStatut() === 'en_attente';
            });

        $refuser = Action::new('refuser', '❌ Refuser', 'fa fa-times')
            ->linkToCrudAction('refuserCommentaire')
            ->setCssClass('btn btn-danger btn-sm')
            ->displayIf(static function (Comment $commentaire): bool {
                return $commentaire->getStatut() === 'en_attente';
            });

        return $actions
            // Ajoute les deux boutons dans la liste (vue "index")
            ->add(Crud::PAGE_INDEX, $valider)
            ->add(Crud::PAGE_INDEX, $refuser);
    }   
    public function configureFields(string $pageName): iterable
    {
        return [
        AssociationField::new('article')
            ->setLabel('Article commenté'),
        
        AssociationField::new('auteur')
            ->setLabel('Auteur du commentaire')
            ->hideOnForm(), // auto-défini au dépôt, pas modifiable en modération
        
        TextareaField::new('contenu')
            ->setLabel('Contenu')
            ->setMaxLength(80), // tronque dans la liste
        
        ChoiceField::new('statut')
            ->setLabel('Statut')
            ->setChoices([
                'En attente' => 'en_attente',
                'Validé' => 'valide',
                'Refusé' => 'refuse',
            ])
            ->renderAsBadges([
                'en_attente' => 'warning',
                'valide' => 'success',
                'refuse' => 'danger',
            ]),
        
        DateTimeField::new('dateCreation')
            ->setLabel('Déposé le')
            ->setFormat('dd/MM/yyyy HH:mm')
            ->hideOnForm(),
    ];
    }
    //Pour passer un commentaire en accepté (statut 'valide') via un bouton 
    #[AdminRoute(path: '/{entityId}/valider')] // Ajout du chemin adminroute pour le CRUD (Changement du EasyAdmin 5.8 sinon ça plante)
    public function validerCommentaire(
        AdminContext $context,
        EntityManagerInterface $em,
    ): Response {
        /** @var Comment $commentaire */
        $commentaire = $context->getEntity()->getInstance();
        $commentaire->setStatut('valide');
        $em->flush();

        // Flash message qui apparaît en haut de la page de redirection
        $this->addFlash('success', sprintf(
            'Commentaire de "%s" validé — il est maintenant visible publiquement.',
            $commentaire->getAuteur()->getEmail()
        ));

        // Redirection vers la liste des commentaires
        return $this->redirect(
            $this->container->get(AdminUrlGenerator::class)
                ->setController(self::class)
                ->setAction(Action::INDEX)
                ->generateUrl()
        );
    }
    // même chose pour l'action inversé rejeté le commentaire.
    #[AdminRoute(path: '/{entityId}/refuser')] // Idem pour le refus sinon la fonction bloque le fonctionnement de l'admin
    public function refuserCommentaire(
        AdminContext $context,
        EntityManagerInterface $em,
    ): Response {
        /** @var Comment $commentaire */
        $commentaire = $context->getEntity()->getInstance();
        $commentaire->setStatut('refuse');
        $em->flush();

        $this->addFlash('warning', sprintf(
            'Commentaire de "%s" refusé.',
            $commentaire->getAuteur()->getEmail()
        ));

        return $this->redirect(
            $this->container->get(AdminUrlGenerator::class)
                ->setController(self::class)
                ->setAction(Action::INDEX)
                ->generateUrl()
        );
    }
}
