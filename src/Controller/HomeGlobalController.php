<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class HomeGlobalController extends AbstractController
{
    #[Route('/front/home', name: 'app_home_global')]
    public function index(Security $security): Response
    {
        // Get the logged-in user
        $user = $security->getUser();

        // Optionally, redirect if not logged in (for security)
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // You can pass user details or any other data you need
        return $this->render('home_global/index.html.twig', [
            'user' => $user,
        ]);
    }
}
