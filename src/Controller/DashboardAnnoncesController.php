<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Form\CreerAnnoncesType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;



class DashboardAnnoncesController extends AbstractController
{
    #[Route('/dashboard/annonces', name: 'dashboard_annonces')]

    public function index(Request $request, ManagerRegistry $mr, SluggerInterface $slugger): Response
    {
        $manager = $mr->getManager();
        $annonces = new Annonces();

        $form = $this->createForm(CreerAnnoncesType::class, $annonces);

        //Est ce que le form a été soumis
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {

            $form->submit($request->request->get($form->getName()));

            if ($form->isSubmitted() && $form->isValid()) {
                /** @var UploadedFile $imageFile */
                $imageFile = $form->get('image')->getData();
                // this condition is needed because the 'image' field is not required
                // so the PDF file must be processed only when a file is uploaded
                if ($imageFile) {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                    // Move the file to the directory where image are stored
                    try {
                        $imageFile->move(
                            $this->getParameter('annonces_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }

                    // updates the 'image' property to store the PDF file name
                    // instead of its contents
                    $annonces->setimage($newFilename);
                }

                $annonces = $form->getData();

                $manager->persist($annonces);
                $manager->flush();

                return $this->redirectToRoute('task_success');
            }
        }

        return $this->render('dashboard_annonces/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
