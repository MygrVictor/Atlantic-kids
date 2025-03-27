<?php 
// src/Controller/SearchController.php
namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\VideoRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function search(Request $request, ArticleRepository $articleRepository, VideoRepository $videoRepository): Response
    {
        $query = $request->query->get('query'); // Récupère la requête de recherche

        // Recherche dans les articles et vidéos
        $articles = $articleRepository->findBySearchQuery($query);
        $videos = $videoRepository->findBySearchQuery($query);

        // Retourne une vue avec les résultats
        return $this->render('search/results.html.twig', [
            'query' => $query,
            'articles' => $articles,
            'videos' => $videos,
        ]);
    }
}
