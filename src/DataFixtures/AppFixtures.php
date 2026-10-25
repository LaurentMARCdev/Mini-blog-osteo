<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Fixtures de démonstration pour le blog d'histoire de l'ostéopathie.
 *
 * Crée en base de données :
 *  - 2 utilisateurs (1 admin + 1 lecteur lambda)
 *  - 4 catégories thématiques
 *  - 5 articles de démo (Lorem Ipsum à ce stade)
 *  - 3 commentaires (2 validés + 1 en attente de modération)
 *
 * Pour recharger ces données en vidant la base :
 *   php bin/console doctrine:fixtures:load
 *
 * Pour les charger SANS vider la base (append) :
 *   php bin/console doctrine:fixtures:load --append
 */

class AppFixtures extends Fixture
{
    /**
     * On injecte deux services dont la fixture a besoin :
     *
     *  - UserPasswordHasherInterface : pour hasher les mots de passe
     *    (JAMAIS en clair en BDD, même en dev)
     *
     *  - SluggerInterface : pour transformer "Histoire de l'ostéopathie"
     *    en "histoire-de-l-osteopathie" (URL-friendly, SEO-friendly)
     */
    public function __construct(
        private UserPasswordHasherInterface $hasher,
        private SluggerInterface $slugger,
    ) {}
    public function load(ObjectManager $manager): void
    {
        // 1. Utilisateurs

        $admin = new User();
        $admin->setEmail('admin@blog-osteo.test');
        $admin->setRoles(['ROLE_ADMIN']);
        // Hashage du mdp avec l'algo configuré dans security.yaml
        $admin->setPassword($this->hasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        $lecteur = new User();
        $lecteur->setEmail('lecteur@blog-osteo.test');
        $lecteur->setRoles(['ROLE_USER']);
        $lecteur->setPassword($this->hasher->hashPassword($lecteur, 'lecteur123'));
        $manager->persist($lecteur);

        //2. Catégories
        $categoriesData = [
            'histoire' => [
                'nom'=> 'Histoire de l\'ostéopathie',
                'description' => 'Les origines américaines, l\'évolution de la discipline, les écoles.',
            ],
            'techniques' => [
                'nom' => 'Techniques et pratiques',
                'description' => 'Approches structurelles, viscérales, crâniennes, fasciales.',
            ],
            'figures' => [
                'nom' => 'Figures marquantes',
                'description' => 'A.T. Still, J.M. Littlejohn, W.G. Sutherland et les autres pionners.',
            ],
            'actus' => [
                'nom' => 'Actualités du blog',
                'description' => 'Nouveautés, évènements, retours de conférences.',
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $cle => $data) {
            $cat = new Category();
            $cat->setNom($data['nom']);
            $cat->setDescription($data['description']);
            // permet de générer un slug automatiquement à partir du nom
            $cat->setSlug($this->slugger->slug($data['nom'])->lower()->toString());
            $manager->persist($cat);
            $categories[$cle] = $cat;
        }

        //3.articles
        // Remplissage temporaire avec du lorem ipsum le temps de vérifier la focntionnalité du code:
            $loremContent = <<<HTML
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Andrew Taylor Still, 
fondateur de l'ostéopathie, a développé cette approche thérapeutique à la fin 
du XIXe siècle dans le Missouri.</p>

<p>Nulla facilisi. Sed ut perspiciatis unde omnis iste natus error sit voluptatem 
accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo 
inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>

<p>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis 
praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias 
excepturi sint occaecati cupiditate non provident.</p>
HTML;

        $articlesData = [
            [
                'titre' => 'Les origines de l\'ostéopathie en 1874',
                'extrait' => 'Comment A.T. Still a formulé pour la première fois les principes de l\'osteopathie dans le Missouri rural.',
                'categorie' => 'histoire',
                'joursAvant' => 15,
            ],
            [
                'titre' => 'Andrew Taylor Still, le fondateur',
                'extrait' => 'Portrait du médecin américain qui a révolutionné l\'approche manuelle du corps humain.',
                'categorie' => 'figures',
                'joursAvant' => 10,
            ],
            [
                'titre' => 'L\'école de Kirksville et son héritage',
                'extrait' => 'La première école d\'ostéopathie fondée en 1892 : programme, étudiants, controverses.',
                'categorie' => 'histoire',
                'joursAvant' => 7,
            ],
            [
                'titre' => 'William Garner Sutherland et l\'approche crânienne',
                'extrait' => 'Comment un élève de Still a étendu l\'ostéopathie au mouvement respiratoire primaire.',
                'categorie' => 'figures',
                'joursAvant' => 3,
            ],
            [
                'titre' => 'Bienvenue sur le blog',
                'extrait' => 'Pourquoi j\'ouvre cet espace de partage sur l\'histoire et les pratiques ostéopathiques.',
                'categorie' => 'actus',
                'joursAvant' => 1,
            ],

        ];

        $articles = [];
        foreach ($articlesData as $data) {
            $article = new Article();
            $article->setTitre($data['titre']);
            $article->setSlug($this->slugger->slug($data['titre'])->lower()->toString());
            $article->setExtrait($data['extrait']);
            $article->setContenu($loremContent);
            //image à voir plus tard avec EasyAdmin (pas essentiel dans un premier temps)
            $datePublication = new \DateTimeImmutable("-{$data['joursAvant']} days");
            $article->setDatePublication($datePublication);
            $article->setDateCreation($datePublication);
            $article->setDateModification(null);
            $article->setAuteur($admin);
            $article->setCategorie($categories[$data['categorie']]);
            $article->setPublie(true);
            $manager->persist($article);
            $articles[] = $article;
        }

        //4.commentaire
        // 2 commentaires validés sur le premier article + 1 en attente
        $commentairesData = [
            [
                'contenu' => 'Très intéressant comme introduction ! J\'avais toujours entendu parler de Still sans vraiment connaître le contexte.',
                'statut' => 'valide',
                'auteur' => $lecteur,
                'article' => $articles[0],
            ],
            [
                'contenu' => 'Merci pour ce rappel historique précieux. Vivement la suite !',
                'statut' => 'valide',
                'auteur' => $lecteur,
                'article' => $articles[0],
            ],
            [
                'contenu' => 'Est-ce que vous allez aussi parler des écoles européennes ? Et de Littlejohn à Londres ?',
                'statut' => 'en_attente',
                'auteur' => $lecteur,
                'article' => $articles[2],
            ],
        ];

        foreach ($commentairesData as $data) {
            $comment = new Comment();
            $comment->setContenu($data['contenu']);
            $comment->setStatut($data['statut']);
            $comment->setDateCreation(new \DateTimeImmutable());
            $comment->setAuteur($data['auteur']);
            $comment->setArticle($data['article']);
            $manager->persist($comment);
        }


        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
