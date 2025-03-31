<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Article;
use App\Entity\Tag;

#[ORM\Entity]
#[ORM\Table(name: 'article_tag')] // Table de liaison entre article et tag
class ArticleTag
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Article::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Article $article = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Tag::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tag $tag = null;

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

        return $this;
    }

    public function getTag(): ?Tag
    {
        return $this->tag;
    }

    public function setTag(?Tag $tag): self
    {
        $this->tag = $tag;

        return $this;
    }
}
