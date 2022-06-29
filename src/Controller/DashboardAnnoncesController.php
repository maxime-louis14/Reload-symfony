<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Form\CreerAnnoncesType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardAnnoncesController extends AbstractController
{
    #[Route('/dashboard/annonces', name: 'dashboard_annonces')]

    public function index(Request $request, ManagerRegistry $mr): Response
    {
        $manager = $mr->getManager();
        $annonces = new Annonces();

        $form = $this->createForm(CreerAnnoncesType::class, $annonces);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $annonces = $form->getData();

            
            $manager->persist($annonces);
            $manager->flush();


            //return $this->redirectToRoute('task_success');
        }

        return $this->render('dashboard_annonces/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
