<?php 

// src/Controller/Admin/DashboardController.php
namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Video;
use App\Entity\Tag;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    private $adminUrlGenerator;

    public function __construct(AdminUrlGenerator $adminUrlGenerator)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    public function index(): Response
    {
        // Rediriger vers la liste des utilisateurs
        $url = $this->adminUrlGenerator->setController(UserCrudController::class)->generateUrl();
        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Atlantic Kids');
    }

   
        public function configureMenuItems(): iterable
{
    yield MenuItem::section('General');
    yield MenuItem::linkToCrud('Users', 'fas fa-user', User::class);
    yield MenuItem::linkToCrud('Articles', 'fas fa-newspaper', Article::class);
    yield MenuItem::linkToCrud('Comments', 'fas fa-comments', Comment::class);

    yield MenuItem::section('Media');
    yield MenuItem::linkToCrud('Videos', 'fas fa-video', Video::class);
  

    yield MenuItem::section('Settings');
    yield MenuItem::linkToRoute('Profile', 'fas fa-cog', 'app_profile');
    yield MenuItem::linkToLogout('Logout', 'fas fa-sign-out-alt');
}

    }

