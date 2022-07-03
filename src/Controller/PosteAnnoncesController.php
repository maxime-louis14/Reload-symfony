<?php

namespace App\Controller;

use DateTime;
use App\Entity\Images;
use DateTimeImmutable;
use App\Form\AnnoncesType;
use App\Entity\AnnoncesUser;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AnnoncesUserRepository;
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
            ->getRepository(AnnoncesUser::class)
            ->findAll();

        return $this->render('poste_annonces/index.html.twig', [
            'annonces' => $annonces,
        ]);
    }

    #[Route('/new', name: 'app_poste_annonces_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $annonce = new AnnoncesUser();
        $form = $this->createForm(AnnoncesType::class, $annonce);
        $form->remove('createdAt');
        $form->remove('updatedAt');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // on recupère les images transmises
            $images = $form->get('images')->getData();
            // On boulcle sur les images
            foreach ($images as $image) {
                //On gener eun nouveau nom de fichier pour
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();
                //on va copier de fichier dans le dossier annonce
                $image->move(
                    $this->getParameter('annonces_directory'),
                    $fichier
                );

                //On sotcke l'images dans la BDD (Son nom)
                $img = new Images();
                $img->setName($fichier);
                $annonce->addImage($img);
            }
            
            $date = new DateTimeImmutable();
            $annonce->setCreatedAt($date);
            $annonce->setUpdatedAt($date);
            $entityManager->persist($annonce);
            $entityManager->flush();

            return $this->redirectToRoute('app_poste_annonces_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('poste_annonces/new.html.twig', [
            'AnnoncesUser' => $annonce,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_poste_annonces_show', methods: ['GET'])]
    public function show(AnnoncesUser $annonce): Response
    {
        return $this->render('poste_annonces/show.html.twig', [
            'AnnoncesUser' => $annonce,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_poste_annonces_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AnnoncesUser $annonce, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AnnoncesType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_poste_annonces_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('poste_annonces/edit.html.twig', [
            'AnnoncesUser' => $annonce,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_poste_annonces_delete', methods: ['POST'])]
    public function delete(Request $request, AnnoncesUser $annonce, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $annonce->getId(), $request->request->get('_token'))) {
            $entityManager->remove($annonce);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_poste_annonces_index', [], Response::HTTP_SEE_OTHER);
    }
}
