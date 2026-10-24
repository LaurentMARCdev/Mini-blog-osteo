<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
 * Récupère les N derniers articles publiés, du plus récent au plus ancien.
 * 
 * On filtre sur publie = true pour ne pas afficher les brouillons sur
 * la page publique, et on trie par date de publication décroissante 'a.datePublication','DESC' .
 *
 * @param int $limite Nombre maximum d'articles à retourner (défaut 3)
 * @return Article[]
 */
public function findLatestPublished(int $limite = 3): array
{
    return $this->createQueryBuilder('a')
        // On ne garde que les articles publiés (pas les brouillons)
        ->where('a.publie = :publie')
        ->setParameter('publie', true)
        // Du plus récent au plus ancien
        ->orderBy('a.datePublication', 'DESC')
        ->setMaxResults($limite)
        ->getQuery()
        ->getResult();
}
public function findAllPublished(): array
{
    return $this->createQueryBuilder('a')
        ->where('a.publie = :publie')
        ->setParameter('publie', true)
        ->orderBy('a.datePublication', 'DESC')
        ->getQuery()
        ->getResult();
}

    //    public function findOneBySomeField($value): ?Article
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
