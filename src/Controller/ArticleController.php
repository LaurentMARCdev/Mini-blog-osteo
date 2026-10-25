<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentFormType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
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
 * GET et POST /article/{slug}
 *
 * - GET : affiche l'article + ses commentaires + éventuellement le formulaire
 * - POST : traite la soumission du formulaire de commentaire (si connecté)
 */
#[Route('/article/{slug}', name: 'app_article_show')]
public function show(
    #[MapEntity(mapping: ['slug' => 'slug'])]
    Article $article,
    CommentRepository $commentRepository,
    Request $request,
    EntityManagerInterface $em,
): Response {
    if (!$article->isPublie()) {
        throw $this->createNotFoundException('Cet article n\'est pas publié.');
    }

    // Les commentaires VALIDÉS pour affichage public
    $commentairesValides = $commentRepository->findValidatedByArticle($article);

    // ═══════════════════════════════════════════
    // Préparation du formulaire de commentaire
    // ═══════════════════════════════════════════
    // On crée systématiquement le form (même si pas connecté) pour que la
    // variable "commentaireForm" existe dans le template. Mais on ne traitera
    // la soumission que si l'utilisateur est bien authentifié (getUser).
    $commentaire = new Comment();
    $form = $this->createForm(CommentFormType::class, $commentaire);

    // Traitement de la soumission UNIQUEMENT si connecté
    if ($this->getUser()) {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hydrate les champs invisibles du formulaire
            $commentaire->setAuteur($this->getUser());
            $commentaire->setArticle($article);
            // Tous les commentaires passent en modération par défaut
            $commentaire->setStatut('en_attente');
            $commentaire->setDateCreation(new \DateTimeImmutable());

            $em->persist($commentaire);
            $em->flush();

            // Flash message : affiché sur la page de redirection
            $this->addFlash('success', 'Merci ! Votre commentaire a été envoyé et sera publié après validation.');

            // POST-Redirect-GET : évite qu'un rafraîchissement du navigateur
            // renvoie le formulaire une seconde fois
            return $this->redirectToRoute('app_article_show', [
                'slug' => $article->getSlug(),
            ]);
        }
    }

    return $this->render('article/show.html.twig', [
        'article' => $article,
        'commentaires' => $commentairesValides,
        'commentaireForm' => $form,
    ]);
}
}