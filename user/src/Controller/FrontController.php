<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    #[Route('/', name: 'front_home')]
    public function index(): Response
    {
        // Vous pouvez passer des données à votre template si nécessaire
        return $this->render('front/home.html.twig');
    }

    #[Route('/booking', name: 'front_booking')]
    public function booking(): Response
    {
        // La page de réservation
        return $this->render('front/booking.html.twig');
    }
    
    #[Route('/complaints', name: 'front_complaints')]
    public function complaints(): Response
    {
        // La page où les clients peuvent envoyer leurs réclamations
        return $this->render('front/complaints.html.twig');
    }
    
    #[Route('/reviews', name: 'front_reviews')]
    public function reviews(): Response
    {
        // La page des avis clients
        return $this->render('front/reviews.html.twig');
    }

    // Vous pouvez ajouter d'autres actions si nécessaire (stations, trains, itinéraires...)
}
