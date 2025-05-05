<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AvisReactionController extends AbstractController
{
    #[Route('/avis/reaction', name: 'app_avis_reaction')]
    public function index(): Response
    {
        return $this->render('avis_reaction/index.html.twig', [
            'controller_name' => 'AvisReactionController',
        ]);
    }
}
