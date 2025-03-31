<?php

namespace App\Controller;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


final class UserController extends AbstractController
{private $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        // Récupérer les articles de l'utilisateur
        $articles = $this->articleRepository->findBy(['user' => $user]);

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'user' => $user,
            'articles' => $articles,
        ]);
    }
    #[Route('/users/update-profile', name: 'update_profile', methods: ['POST'])]

    public function updateProfile(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour modifier votre profil.');
        }

        // Récupérer les valeurs du formulaire
        $bio = $request->request->get('bio');
        $username = $request->request->get('username');

        // Vérifier si le nom d'utilisateur est vide ou déjà pris
        if ($username && $username !== $user->getUsername()) {
            $existingUser = $em->getRepository(User::class)->findOneBy(['username' => $username]);
            if ($existingUser) {
                $this->addFlash('error', 'Ce nom d\'utilisateur est déjà pris.');
                return $this->redirectToRoute('app_user');
            }
            $user->setUsername($username);
        }

        if ($bio) {
            $user->setBio($bio);
        }

        // Gestion de l'upload de la photo de profil
        $profilePicture = $request->files->get('profil_picture');
        dump($profilePicture); // Ajoute cette ligne
die(); // Pour stopper l'exécution et voir le dump
        if ($profilePicture) {
            $newFilename = uniqid() . '.' . $profilePicture->guessExtension();
            try {
                $profilePicture->move(
                    $this->getParameter('profil_pictures_directory'),
                    $newFilename
                );
                $user->setProfilPicture($newFilename);
            } catch (FileException $e) {
                $this->addFlash('error', 'Erreur lors du téléchargement de l\'image.');
            }
        }

        // Enregistrer les changements
        $em->persist($user);
        $em->flush();

        $this->addFlash('success', 'Profil mis à jour avec succès !');
        return $this->redirectToRoute('app_user');
    }
}
