<?php
// src/Controller/VideoController.php

namespace App\Controller;

use App\Entity\Video;
use App\Form\VideoType;
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
        // Récupération de toutes les vidéos
        $videos = $this->entityManager
            ->getRepository(Video::class)
            ->findAll();

        // Affichage de la liste des vidéos
        return $this->render('video/index.html.twig', [
            'videos' => $videos,
        ]);
    }

    #[Route('/video/create', name: 'video_create')]
    public function create(Request $request): Response
    {
        // Si l'utilisateur n'est pas connecté, redirige-le vers la page de login
        $user = $this->getUser();
        if (!$user) {
            // Affiche un message d'erreur ou redirige vers une page de connexion
            $this->addFlash('error', 'Vous devez être connecté pour publier une vidéo.');
            return $this->redirectToRoute('app_login'); // Assurez-vous que la route de login est correcte
        }
    
        $video = new Video();
        $form = $this->createForm(VideoType::class, $video);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if (!$user) {
                // Si l'utilisateur n'est pas connecté, tu peux rediriger ou afficher un message
                $this->addFlash('error', 'Utilisateur non authentifié.');
                return $this->redirectToRoute('app_login'); // Ou rediriger vers la page de connexion
            }
            // Associer l'utilisateur connecté à la vidéo
            $video->setUser($user);
            $video->setCreatedAt(new \DateTimeImmutable());

            // Extraction de l'ID YouTube de l'URL
            $url = $video->getUrl();
            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.*\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $matches);
            $videoId = $matches[1] ?? null;

            if ($videoId) {
                // Générer l'iframe et la vignette
                $iframeUrl = "https://www.youtube.com/embed/{$videoId}";
                $thumbnail = "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg";

                // Sauvegarder l'iframe et la vignette dans la base de données
                $video->setUrl($iframeUrl);
                $video->setThumbnail($thumbnail);
            } else {
                // Si l'URL n'est pas valide, tu pourrais gérer une erreur ou un message
                $this->addFlash('error', 'L\'URL de la vidéo YouTube est invalide.');
                return $this->redirectToRoute('video_create'); // Rediriger pour réessayer
            }

            // Persist la vidéo en base de données
            $this->entityManager->persist($video);
            $this->entityManager->flush();
    
            // Rediriger vers la page de la vidéo
            return $this->redirectToRoute('video_show', ['id' => $video->getId()]);
        }
    
        return $this->render('video/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/video/{id}', name: 'video_show')]
    public function show(int $id): Response
    {
        // Récupération de la vidéo à partir de l'EntityManager
        $video = $this->entityManager
            ->getRepository(Video::class)
            ->find($id);

        // Vérification si la vidéo existe
        if (!$video) {
            throw $this->createNotFoundException('Vidéo non trouvée');
        }

        // Affichage de la vidéo
        return $this->render('video/show.html.twig', [
            'video' => $video,
        ]);
    }
}