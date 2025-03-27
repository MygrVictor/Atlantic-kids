<?php

// src/Entity/Tag.php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: TagRepository::class)]
#[ORM\Table(name: 'tag')]  // Nom de la table Tag
class Tag
{
    #[ORM\ManyToMany(targetEntity: 'App\Entity\Article', mappedBy: 'tags')]
    private Collection $articles;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;

    public function __construct()
    {
        $this->articles = new ArrayCollection(); // Initialisation de la collection
    }

    // Getter et Setter pour l'ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter et Setter pour le nom du tag
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    // Getter pour la collection d'articles associés
    public function getArticles(): Collection
    {
        return $this->articles;
    }
}
