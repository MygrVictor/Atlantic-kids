<?php // src/Repository/TagRepository.php
namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    // Ajouter une méthode de recherche par nom
    public function findByNameLike($searchTerm)
    {
        // Utilisation du query builder pour rechercher les tags contenant $searchTerm dans leur nom
        return $this->createQueryBuilder('t')
            ->where('t.name LIKE :searchTerm')
            ->setParameter('searchTerm', '%' . $searchTerm . '%') // Le % permet de chercher des correspondances partielles
            ->getQuery()
            ->getResult();
    }
}
