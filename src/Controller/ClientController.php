<?php

namespace App\Controller;

use App\Repository\ItineraireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\StationRepository;



final class ClientController extends AbstractController
{
    #[Route('/client', name: 'app_client')]
    public function index(): Response
    {
        return $this->render('client/index.html.twig', [
            'controller_name' => 'ClientController',
        ]);
    }

    #[Route('/client/itineraires', name: 'app_client_itineraires')]
    public function itineraire(ItineraireRepository $itRepo): Response
    {
        $itineraires = $itRepo->findAll();

        return $this->render('client/itineraires.html.twig', [
            'itineraires' => $itineraires
        ]);
    }
    #[Route('/stations/itineraire/{id}', name: 'app_stations_by_itineraire')]
public function stationsByItineraire(
    int $id,
    StationRepository $stationRepo,
    ItineraireRepository $itineraireRepo
): Response {
    $itineraire = $itineraireRepo->find($id);
    $stations = $stationRepo->findBy(['id' => $id]);

    return $this->render('client/stations.html.twig', [
        'itineraire' => $itineraire,
        'stations' => $stations,
    ]);
}

   

    
}
