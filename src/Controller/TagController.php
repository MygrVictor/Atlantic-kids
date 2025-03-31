<?php
namespace App\Controller;

use App\Repository\TagRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TagController extends AbstractController
{
    #[Route('/tags/search', name: 'tag_search', methods: ['GET'])]
    public function searchTags(Request $request, TagRepository $tagRepository): JsonResponse
    {
        $query = $request->query->get('query', '');  // Récupère la requête 'query' de l'URL
        $tags = $tagRepository->findByNameLike($query); // Filtre par nom de tag

        $tagNames = array_map(fn($tag) => $tag->getName(), $tags);

        return new JsonResponse($tagNames);
    }
}