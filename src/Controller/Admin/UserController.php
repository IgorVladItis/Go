<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\EditProductFormType;
use App\Form\EditUserFormType;
use App\Repository\UserRepository;
use Couchbase\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/user', name: 'admin_user_')]
class UserController extends AbstractController
{
    #[Route('/list', name: 'list')]
    public function list(UserRepository $userRepository): Response
    {
        $users = $userRepository->findBy(['isDeleted' => false], ['id' => 'DESC']);

        return $this->render('admin/user/list.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    #[Route('/add', name: 'add')]
    public function edit(Request $request, User $user = null): Response
    {
        if(!$user){
            $user = new User();
        }

        $form = $this->createForm(EditUserFormType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            dd($user);

            $this->addFlash('success', 'Your changes is saved');

            return $this->redirectToRoute('admin_user_edit', ['id' => $user->getid()]);
        }

        if($form->isSubmitted() && !$form->isValid()){
            $this->addFlash('warning', 'Something went wrong');
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(User $user): Response
    {
        if ($user->isSubmitted())
        $user->setIsDeleted(true);
        $this->addFlash('warning', 'The user was successfully deleted!');

        return $this->redirectToRoute('admin_user_list');
    }
}