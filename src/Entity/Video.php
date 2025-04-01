<?php

namespace App\Entity;
use App\Entity\Like;
use App\Entity\User;
use App\Repository\VideoRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotNull;

#[ORM\Entity(repositoryClass: VideoRepository::class)]
#[Broadcast]
#[ORM\HasLifecycleCallbacks] // Ajout de cette annotation pour activer les callbacks lifecycle
class Video 
{

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt; 


     /**
     * @ORM\Column(type="string")
     */
    private $type;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $title = null;

    #[Assert\Url(message: "L'URL de la vidéo n'est pas valide.")]
#[ORM\Column(length: 500)]
private ?string $url = null;


    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\OneToMany(mappedBy: 'likeable', targetEntity: Like::class)]
    private Collection $likes;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $thumbnail;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
    public function __construct()
    {
        $this->likes = new ArrayCollection();
    }

    // Getter et setter pour l'id
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter et setter pour le titre
    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    // Getter et setter pour l'url
    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    // Getter et setter pour la description
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }


    
    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        if (null === $this->created_at) {
            $this->created_at = new \DateTimeImmutable();
        }
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

    public function getIframeUrl(): ?string
    {
        if (preg_match('#(https?://)?(www\.)?(youtube|youtu|youtube-nocookie)\.(com|be)/.*(?:v=|\/)([a-zA-Z0-9_-]+)#', $this->url, $matches)) {
            $videoId = $matches[4]; // ID de la vidéo
            return 'https://www.youtube.com/embed/' . $videoId;
        }
        
    
        return null;
    }
    

    // Getter
    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    // Setter
    public function setThumbnail(?string $thumbnail): self
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }
    public function getLikes(): array
{
    return $this->likes->toArray();
}

public function addLike(Like $like): self
{
    if (!$this->likes->contains($like)) {
        $this->likes[] = $like;
    }

    return $this;
}

public function removeLike(Like $like): self
{
    $this->likes->removeElement($like);
    return $this;
}
public function getCreatedAt(): ?\DateTimeImmutable
{
    return $this->created_at;
}

public function setCreatedAt(?\DateTimeImmutable $createdAt): self
{
    $this->created_at = $createdAt;
    return $this;
}
public function getType(): ?string
    {
        return $this->type;
    }

    // Setter
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}