<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
#[ORM\UniqueConstraint(name: 'email_unique', columns: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'Il y a déjà un compte avec cet email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
 * @OneToMany(targetEntity="App\Entity\Article", mappedBy="user")
 */
private $articles;

    // Ajout de la propriété roles
    #[ORM\Column(type: "json")]
    private array $roles = [];

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $firstname = null;

    // Nouvel attribut username
    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Like::class)]
    private Collection $likes;

    #[ORM\Column]
    private bool $isVerified = false;

    #[ORM\Column(type: "string", nullable: false)]
private ?string $profilPicture = 'default.jpg'; // Assurez-vous de définir une valeur par défaut ou de la rendre nullable


    #[ORM\Column(length: 255, nullable: true)]
    private ?string $bio = null;

    #[ORM\OneToMany(mappedBy: "user", targetEntity: "App\Entity\Video")]
private Collection $videos;

 /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    public function __construct()
    {
        $this->likes = new ArrayCollection();
        $this->videos = new ArrayCollection();
        $this->articles = new ArrayCollection();
    }

    // Getters et setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    // Méthode pour récupérer les likes de l'utilisateur
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    // Méthode __toString pour afficher le nom complet de l'utilisateur
    public function __toString(): string
    {
        return $this->firstname . ' ' . $this->name;
    }

    // Implémentation de UserInterface
    public function getRoles(): array
    {
        $roles = $this->roles;
        // Chaque utilisateur doit avoir au moins un rôle
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function eraseCredentials(): void
    {
        // Effacer les données sensibles si nécessaire
    }

    public function getUserIdentifier(): string
    {
        return $this->email; // Email comme identifiant
    }

    // Nouveau getter et setter pour le username
    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;
        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getProfilPicture(): ?string
    {
        return $this->profilPicture;
    }

    public function setProfilPicture(string $profilPicture): self
    {
        $this->profilPicture = $profilPicture;
        return $this;
    }


    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): static
    {
        $this->bio = $bio;

        return $this;
    }
    public function getArticles(): Collection
{
    return $this->articles;
}
public function getVideos(): Collection
{
    return $this->videos;
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

}
