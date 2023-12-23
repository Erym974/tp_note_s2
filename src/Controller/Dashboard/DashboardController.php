<?php

namespace App\Controller\Dashboard;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\Report;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_MODERATOR')]
class DashboardController extends AbstractDashboardController
{
    #[Route('/dashboard', name: 'dashboard')]
    public function index(): Response
    {
        return $this->render('dashboard/index.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Tp Note S2');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section('Menu');
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToRoute('Go back to the site', 'fa fa-arrow-left', 'home.index');
        yield MenuItem::section('Models');
        yield MenuItem::linkToCrud('Posts', 'fas fa-list', Post::class)->setPermission('ROLE_MODERATOR');
        yield MenuItem::linkToCrud('Comments', 'fas fa-comments', Comment::class)->setPermission('ROLE_MODERATOR');
        yield MenuItem::linkToCrud('Reports', 'fas fa-flag', Report::class)->setPermission('ROLE_MODERATOR');
        yield MenuItem::linkToCrud('Categories', 'fas fa-layer-group', Category::class)->setPermission('ROLE_MODERATOR');
        yield MenuItem::linkToCrud('Users', 'fas fa-users', User::class)->setPermission('ROLE_ADMIN');
    }
}
