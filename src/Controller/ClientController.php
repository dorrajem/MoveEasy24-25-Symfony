<?php

namespace App\Controller;

use App\Repository\ItineraireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\StationRepository;



final class ClientController extends AbstractController
{
    #[Route('/{_locale}/client', name: 'app_client', requirements: ['_locale' => 'fr|en'], defaults: ['_locale' => 'fr'], methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('client/index.html.twig', [
            'controller_name' => 'ClientController',
        ]);
    }

    #[Route('/{_locale}/client/itineraires', name: 'app_client_itineraires', requirements: ['_locale' => 'fr|en'], defaults: ['_locale' => 'fr'])]
    public function itineraire(ItineraireRepository $itRepo): Response
    {
        $itineraires = $itRepo->findAll();

        return $this->render('client/itineraires.html.twig', [
            'itineraires' => $itineraires

        ]);
    }

    
    #[Route('/{_locale}/stations/itineraire/{id}', name: 'app_station_by_itineraire', requirements: ['_locale' => 'fr|en'], defaults: ['_locale' => 'fr'])]
    public function stationsByItineraire(
        int $id,
        StationRepository $stationRepo,
        ItineraireRepository $itineraireRepo
    ): Response {
        $itineraire = $itineraireRepo->findById($id);
        if (!$itineraire) {
            throw $this->createNotFoundException('Itineraire not found.');
        }
    
        // Ensure stations are fetched based on the itinerary
        $stations = $stationRepo->findByItineraire($itineraire->getId());
    
        return $this->render('client/stations.html.twig', [
            'itineraire' => $itineraire,
            'stations' => $stations,
        ]);
    }




    #[Route('/{_locale}/client/maps', name: 'app_client_maps', requirements: ['_locale' => 'fr|en'], defaults: ['_locale' => 'fr'])]
    public function ShowMap(Request $request, StationRepository $stRepo, ItineraireRepository $itiRepo): Response
    {
        $lat = $request->query->get('lat');
$lon = $request->query->get('lon');
$stations = [];

$itineraires = $itiRepo->findAll(); // Assuming you have ItineraireRepository injected
if ($lat && $lon) {
    foreach ($itineraires as $itineraire) {
        $startStation = $itineraire->getStartStation();
        $endStation = $itineraire->getEndStation();

        foreach ([$startStation, $endStation] as $station) {
            if ($station && $station->getStatut() === 'Active') {
                $stations[] = [
                    'id' => $station->getId(),
                    'nom' => $station->getNomStation(),
                    'latitude' => $station->getLatitude(),
                    'longitude' => $station->getLongitude(),
                ];
            }
        }
    }
}

return $this->render('client/maps.html.twig', [
    'stations' => $stations,

]);
    }
   

    
}
