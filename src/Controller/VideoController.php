<?php

namespace App\Controller;

use App\Entity\Video;
use App\Entity\Comment;
use App\Form\VideoType;
use App\Form\CommentType;  // Assurez-vous d'avoir ce formulaire pour Comment
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class VideoController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/video', name: 'video_index')]
    public function index(): Response
    {
        $videos = $this->entityManager
            ->getRepository(Video::class)
            ->findAll();

        return $this->render('video/index.html.twig', [
            'videos' => $videos,
        ]);
    }

    #[Route('/video/create', name: 'video_create', methods: ['GET', 'POST'])]
    public function create(Request $request)
    {
        $video = new Video();
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour publier une vidéo.');
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(VideoType::class, $video, [
            'user' => $user,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $video->setUser($user);
            $video->setCreatedAt(new \DateTimeImmutable());

            // Extraire l'ID YouTube de l'URL
            $url = $video->getUrl();
            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.*\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $matches);
            $videoId = $matches[1] ?? null;

            if ($videoId) {
                $iframeUrl = "https://www.youtube.com/embed/{$videoId}";
                $thumbnail = "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg";
                $video->setUrl($iframeUrl);
                $video->setThumbnail($thumbnail);
            } else {
                $this->addFlash('error', 'L\'URL de la vidéo YouTube est invalide.');
                return $this->redirectToRoute('video_create');
            }

            $this->entityManager->persist($video);
            $this->entityManager->flush();

            return $this->redirectToRoute('video_show', ['id' => $video->getId()]);
        }

        return $this->render('video/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/video/{id}', name: 'video_show', requirements: ['id' => '\d+'])]
    public function show(int $id, Request $request): Response
    {
        $video = $this->entityManager
            ->getRepository(Video::class)
            ->find($id);

        if (!$video) {
            throw $this->createNotFoundException('Vidéo non trouvée');
        }

        // Récupérer les commentaires associés à la vidéo
        $commentRepository = $this->entityManager->getRepository(Comment::class);
        $comments = $commentRepository->findCommentsByTarget(Video::class, $id);

        // Créer un nouveau commentaire
        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setTargetType(Video::class);
            $comment->setTargetId($video->getId());
            $comment->setUser($this->getUser());

            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            return $this->redirectToRoute('video_show', ['id' => $video->getId()]);
        }

        return $this->render('video/show.html.twig', [
            'video' => $video,
            'comments' => $comments,
            'commentForm' => $commentForm->createView(),
        ]);
    }
}