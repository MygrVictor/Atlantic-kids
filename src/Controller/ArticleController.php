<?php
namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment; 
use App\Form\ArticleType;
use App\Form\CommentType; 
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class ArticleController extends AbstractController
{
    #[Route('/article', name: 'app_article')]
    public function index(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findAll();
        return $this->render('article/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/article/create', name: 'article_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour créer un article.');
            return $this->redirectToRoute('app_login');
        }

        $article = new Article();
        $article->setUser($user);

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dump('Formulaire soumis et valide');

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

            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_article');
        }

        return $this->render('article/create.html.twig', [
            'form' => $form->createView(),
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
    #[Route('/article/{id}', name: 'article_show', methods: ['GET', 'POST'])]
public function show(
    int $id, 
    ArticleRepository $articleRepository, 
    CommentRepository $commentRepository, 
    Request $request, 
    EntityManagerInterface $entityManager
): Response {
    $article = $articleRepository->find($id);

    if (!$article) {
        throw $this->createNotFoundException('Article non trouvé');
    }

    // Récupération des commentaires liés à l'article
    $comments = $commentRepository->findCommentsByTarget(Article::class, $id);

   

    // Création du formulaire de commentaire
    $comment = new Comment();
    $commentForm = $this->createForm(CommentType::class, $comment);
    $commentForm->handleRequest($request);

    if ($commentForm->isSubmitted() && $commentForm->isValid()) {
        $comment->setTargetType(Article::class);
        $comment->setTargetId($article->getId());
        $comment->setUser($this->getUser());
        $comment->setCreatedAt(new \DateTime());

        $entityManager->persist($comment);
        $entityManager->flush();

        return $this->redirectToRoute('article_show', ['id' => $article->getId()]);
    }

    return $this->render('article/show.html.twig', [
        'article' => $article,
        'comments' => $comments,
        'commentForm' => $commentForm->createView(),
    ]);
}
    
   


}