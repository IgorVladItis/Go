<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileEditFormType;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'main_profile_index')]
    public function index(): Response
    {
        return $this->render('main/parts/profile/index.html.twig');
    }

    #[Route('/profileEdit', name: 'main_profile_edit')]
    public function editProfile(Request $request, EntityManagerInterface $doctrine): Response
    {

        $user = $this->getUser();
        dd($user);
        $form = $this->createForm(ProfileEditFormType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine;
            $entityManager->persist($user);
            $entityManager->flush();
            $this->logEvent()->info('Пользователь зарегистрирован');

            return $this->redirectToRoute('main_profile_index');
        }

        return $this->render('main/parts/profile/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function logEvent(): Logger
    {
        $logger = new Logger("editProfile");
        $logger->pushHandler(new StreamHandler(__DIR__.'Logs'.'/log_file.log', Level::Info));
        return $logger;
    }
}
