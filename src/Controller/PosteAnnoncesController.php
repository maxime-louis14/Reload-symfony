<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Annonces;
use App\Form\AnnoncesType;
use App\Repository\AnnoncesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/poste/annonces')]
class PosteAnnoncesController extends AbstractController
{

    #[Route('/', name: 'app_poste_annonces_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $annonces = $entityManager
            ->getRepository(Annonces::class)
            ->findAll();

        return $this->render('poste_annonces/index.html.twig', [
            'annonces' => $annonces,
        ]);
    }

    #[Route('/new', name: 'app_poste_annonces_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $annonce = new Annonces();
        $form = $this->createForm(AnnoncesType::class, $annonce);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // On recupère les images transmises
            $images = $form->get('images')->getData;

            //On boucle sur les images trans
            foreach ($images as $image) {
                // On genere un nouveau nom de fichier pour
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();

                // On copie dans le dossier uplode images annonces
                $image->move(
                    $this->getParameter('annonces_directory'),
                    $fichier
                );

                // On stock l'images dans la BDD (Sont nom)
                $img = new Images();
                $img->setName($fichier);
                $annonce->addImage($img);
            }

            $entityManager->persist($annonce);
            $entityManager->flush();

            return $this->redirectToRoute('app_poste_annonces_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('poste_annonces/new.html.twig', [
            'annonce' => $annonce,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_poste_annonces_show', methods: ['GET'])]
    public function show(Annonces $annonce): Response
    {
        return $this->render('poste_annonces/show.html.twig', [
            'annonce' => $annonce,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_poste_annonces_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Annonces $annonce, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AnnoncesType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_poste_annonces_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('poste_annonces/edit.html.twig', [
            'annonce' => $annonce,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_poste_annonces_delete', methods: ['POST'])]
    public function delete(Request $request, Annonces $annonce, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $annonce->getId(), $request->request->get('_token'))) {
            $entityManager->remove($annonce);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_poste_annonces_index', [], Response::HTTP_SEE_OTHER);
    }
}
