<?php

namespace App\Controller\EasyAdmin;

use App\Entity\Question;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/easyadmin', name: 'easyadmin')]
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Pierwszy admin');
    }

    public function configureMenuItems(): iterable
    {

        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linktoRoute('Back to the website', 'fas fa-home', 'app_homepage');
        yield MenuItem::linkToCrud('Questions', 'fas fa-map-marker-alt', Question::class);
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
