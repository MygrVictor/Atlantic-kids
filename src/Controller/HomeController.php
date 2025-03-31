<?php
// src/Controller/HomeController.php

namespace App\Controller;

use App\Repository\VideoRepository;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(VideoRepository $videoRepository, ArticleRepository $articleRepository): Response
    {
        // Récupérer les vidéos et articles
        $videos = $videoRepository->findBy([], ['created_at' => 'DESC']);
        $articles = $articleRepository->findBy([], ['createdAt' => 'DESC']);

        // Fusionner et marquer les objets avec un champ temporaire "type"
        $content = [];

        foreach ($videos as $video) {
            $videoData = (object) ['type' => 'video', 'data' => $video]; // Ajout du type
            
            $content[] = $video;
        }

        foreach ($articles as $article) {
            $article->type = 'article'; // Ajout du type
            $content[] = $article;
        }

        // Trier les éléments par date de création (du plus récent au plus ancien)
        usort($content, function ($a, $b) {
            return $b->getCreatedAt() <=> $a->getCreatedAt();
        });

        return $this->render('home/index.html.twig', [
            'content' => $content,
        ]);
    }
}