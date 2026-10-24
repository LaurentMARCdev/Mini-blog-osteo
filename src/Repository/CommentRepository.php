<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Article;
/**
 * @extends ServiceEntityRepository<Comment>
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function findValidatedByArticle(Article $article): array
    {
    return $this->createQueryBuilder('c')
        ->where('c.article = :article')
        ->andWhere('c.statut = :statut')
        ->setParameter('article', $article)
        ->setParameter('statut', 'valide')
        ->orderBy('c.dateCreation', 'ASC')
        ->getQuery()
        ->getResult();
    }
}
