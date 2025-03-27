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
    public function findAllArticles()
    {
        return $this->findBy([], ['createdAt' => 'DESC']); // Trier les articles par date
    }
    public function findBySearchQuery(string $query)
    {
        // Recherche dans le titre, le contenu, et par utilisateur
        return $this->createQueryBuilder('a')
            ->leftJoin('a.user', 'u') // Joindre l'entité User (si une relation existe)
            ->where('a.title LIKE :query')
            ->orWhere('a.content LIKE :query')
            ->orWhere('u.username LIKE :query') // Recherche aussi dans le nom d'utilisateur
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Article[] Returns an array of Article objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

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
