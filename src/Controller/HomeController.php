<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur de la page d'accueil publique.
 *
 * Affiche les 3 derniers articles publiés, triés par date de publication
 * décroissante.
 */
class HomeController extends AbstractController
{
    /**
     * GET /
     * 
     * J'injecte directement ArticleRepository dans la méthode : Symfony
     * autowire sait récupérer tout service dont on déclare le type en
     * paramètre. Pas besoin de l'injecter dans le constructeur.
     */
    #[Route('/', name: 'app_home')]
    public function index(ArticleRepository $articleRepository): Response
    {
        // j'appelle une méthode custom du repository qu'on va créer juste après : récupère les N derniers articles publiés.
        $derniersArticles = $articleRepository->findLatestPublished(3);

        return $this->render('home/index.html.twig', [
            'articles' => $derniersArticles,
        ]);
    }
}