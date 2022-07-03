<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{

    #[Route('/inscription', name: 'app_register')]
    
    public function index(Request $request, ManagerRegistry $mr, UserPasswordHasherInterface $hasher): Response
    {
        $manager = $mr->getManager();
        $user = new User();

        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);
        $notification = null;

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();

            $password = $hasher->hashPassword($user, $user->getPassword());

            $user->setPassword($password);

            $manager->persist($user);
            $manager->flush();


            return $this->redirectToRoute('app_login');
        }else {
            $notification = "erreur lors de l'inscription";
        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
