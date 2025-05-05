<?php

namespace App\Controller;

use App\Repository\StationRepository;
use App\Repository\ItineraireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\EventListener\ExceptionListener;
class StationApiController extends AbstractController

{
    #[Route('/{_locale}/api/stations', name: 'api_stations', requirements: ['_locale' => 'fr|en'], defaults: ['_locale' => 'fr'], methods: ['GET'])]
    public function getStations(StationRepository $stationRepository): JsonResponse
    {
        $stations = $stationRepository->findAll();

        $data = [];
        foreach ($stations as $station) {
            $data[] = [
                'id' => $station->getId(),
                'name' => $station->getNomStation(), // adapt method name if needed
                'latitude' => $station->getLatitude(),
                'longitude' => $station->getLongitude(),
                'statut' => $station->getStatut() ?? '', // optional
            ];
        }

        return $this->json($data);
    }
    #[Route('/{_locale}/api/itineraires', name: 'api_itineraires', requirements: ['_locale' => 'fr|en'], defaults: ['_locale' => 'fr'], methods: ['GET'])]
public function itinerariesJson(ItineraireRepository $itineraireRepository): JsonResponse
{
    $itineraries = $itineraireRepository->findAll();
    $data = [];

    foreach ($itineraries as $itineraire) {
        $data[] = [
            'id' => $itineraire->getId(),
            'nomItineraire' => $itineraire->getNomItineraire(),
            'distance' => $itineraire->getDistance(),
            'startStation' => [
                'name' => $itineraire->getStartStation()->getNomStation(),
                'latitude' => $itineraire->getStartStation()->getLatitude(),
                'longitude' => $itineraire->getStartStation()->getLongitude(),
            ],
            'endStation' => [
                'name' => $itineraire->getEndStation()->getNomStation(),
                'latitude' => $itineraire->getEndStation()->getLatitude(),
                'longitude' => $itineraire->getEndStation()->getLongitude(),
            ],
        ];
    }

    return $this->json($data);
}

}
