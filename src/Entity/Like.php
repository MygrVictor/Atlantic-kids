<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Like
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    private ?int $likeableId = null;

    #[ORM\Column(length: 255)]
    private ?string $likeableType = null;

    #[ORM\Column(type: "datetime")]
    private \DateTime $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    // Getters et setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLikeableId(): ?int
    {
        return $this->likeableId;
    }

    public function setLikeableId(int $likeableId): self
    {
        $this->likeableId = $likeableId;
        return $this;
    }

    public function getLikeableType(): ?string
    {
        return $this->likeableType;
    }

    public function setLikeableType(string $likeableType): self
    {
        $this->likeableType = $likeableType;
        return $this;
    }
    public function getUser(): ?User
    {
        return $this->user;
    }
    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }
    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
