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
    #[Route('/video/create', name: 'video_create', methods: ['GET', 'POST'])]
    public function create(Request $request)
    {
        // Créer une nouvelle instance de la vidéo
        $video = new Video();

        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Vérifier si l'utilisateur est connecté
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour publier une vidéo.');
            return $this->redirectToRoute('app_login');
        }

        // Créer le formulaire en passant l'objet Video et l'utilisateur comme options
        $form = $this->createForm(VideoType::class, $video, [
            'user' => $user,  // Passer l'utilisateur connecté ici
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // L'utilisateur est déjà assigné à la vidéo via les options du formulaire
            $video->setUser($user); // Assurer que l'utilisateur est bien lié à la vidéo
            $video->setCreatedAt(new \DateTimeImmutable());  // Définir la date de création de la vidéo

            // Extraire l'ID YouTube de l'URL
            $url = $video->getUrl();
            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.*\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $matches);
            $videoId = $matches[1] ?? null;

            if ($videoId) {
                // Générer l'iframe et la vignette de la vidéo
                $iframeUrl = "https://www.youtube.com/embed/{$videoId}";
                $thumbnail = "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg";

                // Mettre à jour l'URL et la vignette de la vidéo
                $video->setUrl($iframeUrl);
                $video->setThumbnail($thumbnail);
            } else {
                // Si l'URL est invalide, afficher un message d'erreur et rediriger vers la page de création
                $this->addFlash('error', 'L\'URL de la vidéo YouTube est invalide.');
                return $this->redirectToRoute('video_create');
            }

            // Persister la vidéo en base de données
            $this->entityManager->persist($video);
            $this->entityManager->flush();

            // Rediriger vers la page de la vidéo nouvellement créée
            return $this->redirectToRoute('video_show', ['id' => $video->getId()]);
        }

        // Afficher le formulaire
        return $this->render('video/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
   

    #[Route('/video/{id}', name: 'video_show', requirements: ['id' => '\d+'])]
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