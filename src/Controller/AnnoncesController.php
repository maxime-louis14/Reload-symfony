<?php

namespace App\Controller;

use App\Repository\AnnoncesUserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnnoncesController extends AbstractController
{
    #[Route('/', name: 'app_annonces')]
    public function index(AnnoncesUserRepository $annoncesRepository): Response
    {
        $annonces = $annoncesRepository -> findAll();

        return $this->render('home\index.html.twig', [
            'controller_name' => 'AnnoncesController',
            'annonces' => $annonces,
        ]);
    }
}
