<?php

namespace App\Repository;

use App\Entity\Video;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Video>
 */
class VideoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Video::class);
    }
    public function findBySearchQuery(string $query)
    {
        // Recherche dans le titre, la description et le nom d'utilisateur associé
        return $this->createQueryBuilder('v')
            ->leftJoin('v.user', 'u') // Jointure avec l'entité User
            ->where('v.title LIKE :query')
            ->orWhere('v.description LIKE :query')
            ->orWhere('u.username LIKE :query') // Recherche par nom d'utilisateur
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();
    }
}
    //    /**
    //     * @return Video[] Returns an array of Video objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('v.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Video
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

