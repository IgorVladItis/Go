<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
//use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{

    #[Route('/registration', name: 'main_registration')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();



            return $this->redirectToRoute('main_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
//    #[Route('/verify/email', name: 'main_verify_email')]
//    public function verifyUserEmail(Request $request, UserRepository $userRepository): Response
//    {
//        $id = $request->get('id');
//
//        if (null === $id) {
//            return $this->redirectToRoute('main_registration');
//        }
//
//        $user = $userRepository->find($id);
//
//        if (null === $user) {
//            return $this->redirectToRoute('main_registration');
//        }
//        try {
//            $this->emailVerifier->handleEmailConfirmation($request, $user);
//
//        } catch (VerifyEmailExceptionInterface $exception) {
//
//            $this->addFlash('warning', $exception->getReason());
//
//            return $this->redirectToRoute('main_homepage');
//        }
//
//        // @TODO Change the redirect on success and handle or remove the flash message in your templates
//        $this->addFlash('success', 'Your email address has been verified.');
//
//        return $this->redirectToRoute('main_homepage');
//    }
//}
