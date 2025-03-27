<?php

namespace App\Controller;
use App\Form\ArticleType;
use App\Entity\Article;
use App\Entity\Tag;
use App\Entity\Like;
use App\Repository\ArticleRepository;
use App\Repository\LikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

final class ArticleController extends AbstractController
{
    private $likeRepository;
    private $entityManager;

    // Injection de LikeRepository et EntityManagerInterface
    public function __construct(LikeRepository $likeRepository, EntityManagerInterface $entityManager)
    {
        $this->likeRepository = $likeRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/article', name: 'app_article')]
    public function index(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findAll();
        return $this->render('article/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/article/create', name: 'article_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour créer un article.');
            return $this->redirectToRoute('app_login');
        }

        $article = new Article();
        $article->setUser($user);

        // Créer le formulaire
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dump('Formulaire soumis et valide');

            // Traitement de l'image
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                    $article->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'upload de l\'image.');
                }
            }

            // Traitement des tags
            $tagsInput = $form->get('tagsInput')->getData(); // Récupère les tags saisis par l'utilisateur
            if ($tagsInput) {
                $tagsArray = explode(',', $tagsInput); // Diviser la chaîne en un tableau

                foreach ($tagsArray as $tagName) {
                    $tagName = trim($tagName); // Retirer les espaces superflus

                    // Cherche si le tag existe déjà dans la base de données
                    $tag = $this->entityManager->getRepository(Tag::class)->findOneBy(['name' => $tagName]);

                    if (!$tag) {
                        // Si le tag n'existe pas, on le crée
                        $tag = new Tag();
                        $tag->setName($tagName);
                        $this->entityManager->persist($tag);
                    }

                    // Ajouter le tag à l'article
                    $article->addTag($tag);
                }
            }

            // Persist l'article avec ses tags associés
            $this->entityManager->persist($article);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_article');
        }

        return $this->render('article/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/article/{id}', name: 'article_show')]
    public function show($id, ArticleRepository $articleRepository): Response
    {
        $article = $articleRepository->find($id);

        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé');
        }

        return $this->render('article/show.html.twig', [
            'post' => $article,
        ]);
    }

    #[Route('/article/{id}/delete', name: 'article_delete', methods: ['POST'])]
    public function delete($id, ArticleRepository $articleRepository, EntityManagerInterface $entityManager): Response
    {
        $article = $articleRepository->find($id);

        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé');
        }

        if ($article->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à supprimer cet article.');
            return $this->redirectToRoute('app_article');
        }

        $entityManager->remove($article);
        $entityManager->flush();

        $this->addFlash('success', 'Article supprimé avec succès.');
        return $this->redirectToRoute('app_article');
    }

    // Refactorisation de la méthode "like"
    #[Route('/articles/{id}/like', name: 'articles_like', methods: ['POST'])]
    public function like(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['message' => 'User not authenticated.'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $article = $entityManager->getRepository(Article::class)->find($id);
        if (!$article) {
            return new JsonResponse(['message' => 'Article introuvable.'], JsonResponse::HTTP_NOT_FOUND);
        }

        $existingLike = $entityManager->getRepository(Like::class)
            ->findOneBy(['article' => $article, 'user' => $user]);

        if ($existingLike) {
            $entityManager->remove($existingLike);
            $entityManager->flush();
        } else {
            $like = new Like();
            $like->setArticle($article);
            $like->setUser($user);
            $entityManager->persist($like);
            $entityManager->flush();
        }

        $likesCount = $entityManager->getRepository(Like::class)->count(['article' => $article]);
        $userHasLiked = $article->userHasLiked($user);

        return new JsonResponse(['liked' => $userHasLiked, 'likesCount' => $likesCount], JsonResponse::HTTP_OK);
    }
}

