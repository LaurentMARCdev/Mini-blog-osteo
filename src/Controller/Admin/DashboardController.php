<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Controller\Admin\ArticleCrudController;
use App\Controller\Admin\CategoryCrudController;
use App\Controller\Admin\CommentCrudController;
use App\Controller\Admin\UserCrudController;


#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        //return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // return $this->redirectToRoute('admin_user_index');

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        return $this->redirectToRoute('admin_article_index');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<strong>Blog Ostéopathie</strong><br><small>Administration</small>');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');
        
        //Section Contenu: Articles & Catégories

        yield MenuItem::section('Contenu');
        yield MenuItem::linkTo( ArticleCrudController::class, 'Articles','fa fa-newspaper')
            ->setAction('index');
        yield MenuItem::linkTo(CategoryCrudController::class, 'Catégories','fa fa-folder')
            ->setAction('index');

        //Section Modération: Commentaires

        yield MenuItem::section('Modération');
        yield MenuItem::linkTo(CommentCrudController::class, 'Commentaires','fa fa-comments')
            ->setAction('index');

        //Section Utilisateurs: gestion des comptes

        yield MenuItem::section('Utilisateurs');
        yield MenuItem::linkTo(UserCrudController::class, 'Comptes','fa fa-user')
            ->setAction('index');

        //Lien retour vers le site public

        yield MenuItem::section('Site');
        yield MenuItem::linkToRoute('Voir le site','fa fa-external-link-alt', 'app_home');

    }
}
