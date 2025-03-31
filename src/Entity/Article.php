<?php 
// src/Entity/Article.php

namespace App\Entity;

use App\Entity\Tag; // Ajoute l'entité Tag
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\ArticleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: "App\Entity\User", inversedBy: "articles")]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "L'utilisateur associé à la publication ne peut pas être nul.")]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: "text")]
    #[Assert\NotBlank]
    private ?string $content = null;


    #[ORM\Column(type: "datetime")]
private \DateTime $createdAt;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable("user_article_like")]
    private Collection $likes;

    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: "articles")]
    #[ORM\JoinTable(name: "article_tags")]
    private Collection $tags;
    

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->likes = new ArrayCollection(); // Initialisation de la collection
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
    }

    // Getters et setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }
    public function getExcerpt(): string
    {
        // Limite l'extrait à 150 caractères, par exemple
        return substr(strip_tags($this->content), 0, 150) . '...';
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;
        return $this;
    }

    public function getCreatedAt(): \DateTime
{
    return $this->createdAt;
}

public function setCreatedAt(\DateTime $createdAt): static
{
    $this->createdAt = $createdAt;
    return $this;
}

    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(User $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
        }

        return $this;
    }

    public function removeLike(User $like): self
    {
        $this->likes->removeElement($like);
        return $this;
    }

    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);
        return $this;
    }
}
