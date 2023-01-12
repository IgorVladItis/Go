<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
    
#[Route(path: '/admin')]
class AdminboardController extends AbstractController
{
    #[Route(path: '/adminboard', name: 'admin_board_show')]
    public function adminBoard(): Response
    {
        return $this->render('admin/pages/admin_board.html.twig');
    }
}