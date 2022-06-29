<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardAnnoncesController extends AbstractController
{
    #[Route('/dashboard/annonces', name: 'dashboard_annonces')]

    public function index(): Response
    {
        return $this->render('dashboard_annonces/index.html.twig',);
    }
}
