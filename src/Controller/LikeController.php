<?php
namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LikeController extends AbstractController
{
    #[Route('/article/{id}/like', name: 'article_like', methods: ['POST'])]
    public function like(Article $article, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['message' => 'Vous devez être connecté pour liker un article.'], 403);
        }

        if ($article->getLikes()->contains($user)) {
            // Si l'utilisateur a déjà liké l'article, on retire le like
            $article->removeLike($user);
        } else {
            // Sinon, on ajoute le like
            $article->addLike($user);
        }

        $entityManager->flush();

        return $this->json(['likes' => $article->getLikes()->count()]);
    }
}
