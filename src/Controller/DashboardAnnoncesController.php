<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Form\CreerAnnoncesType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class DashboardAnnoncesController extends AbstractController
{
    #[Route('/dashboard/annonces', name: 'dashboard_annonces')]

    public function new(Request $request, ManagerRegistry $mr, SluggerInterface $slugger,)
    {
        $manager = $mr->getManager();
        $annonces = new Annonces();
        
        $form = $this->createForm(CreerAnnoncesType::class, $annonces);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $image */
            $image = $form->get('image')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $image->move(
                        $this->getParameter('annonces_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $annonces->setImage($newFilename);
            }

            // ... persist the $product variable or any other work

            // return $this->redirectToRoute('dashboard_annonces');
        }

        return $this->renderForm('dashboard_annonces\index.html.twig', [
            'form' => $form,
        ]);

        $manager->persist($annonces);
        $manager->flush();
    }
}
