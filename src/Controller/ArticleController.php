<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CommentRepository;

/**
 * Contrôleur des articles publics (liste + détail).
 * Note : les routes d'édition/suppression des articles seront ailleurs(via EasyAdmin pour le back-office).
 */
class ArticleController extends AbstractController
{
    /**
     * GET /articles
     * Liste tous les articles publiés findAllPublished, triés du plus récent au plus ancien.
     */
    #[Route('/articles', name: 'app_article_index')]
    public function index(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findAllPublished();

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * GET /article/{slug}
     *
     * Détail d'un article identifié par son slug.
     *
     * Grâce à MapEntity => Symfony détecte automatiquement le paramètre {slug} dans l'URL et va chercher l'Article correspondant en BDD via le repository.
     *  Si pas trouvé → 404 automatique.
     */
    #[Route('/article/{slug}', name: 'app_article_show')]
    public function show(
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Article $article,
        CommentRepository $commentRepository,
    ): Response {
        // Sécurité : on refuse d'afficher un brouillon même si on connaît son slug. Si l'article existe mais n'est pas publié, on renvoie une 404.
        if (!$article->isPublie()) {
            throw $this->createNotFoundException('Cet article n\'est pas publié.');
        }
        $commentairesValides = $commentRepository->findValidatedByArticle($article);
        return $this->render('article/show.html.twig', [
            'article' => $article,
            'commentaires' => $commentairesValides,
        ]);
    }
}