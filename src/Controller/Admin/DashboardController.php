<?php

namespace App\Controller\Admin;

use App\Entity\Annonces;
use App\Entity\Category;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(private AdminUrlGenerator $adminUrlGenerator)
    {
    }
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $url = $this->adminUrlGenerator
            ->setController(CategoryCrudController::class)
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Reload');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Utilisateur', 'fa fa-user', User::class);

        yield MenuItem::section('Category');
        yield MenuItem::subMenu('Category', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Add categories', 'fas fa-plus', Category::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Show categories', 'fas fa-eye', Category::class)
        ]);
        
        yield MenuItem::section('annonces');
        yield MenuItem::subMenu('Annonces', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Create annonces', 'fas fa-plus', Annonces::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Show annonces', 'fas fa-eye', Annonces::class)
        ]);
    }
}
