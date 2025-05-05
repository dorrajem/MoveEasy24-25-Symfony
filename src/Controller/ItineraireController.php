<?php

namespace App\Controller;

use App\Entity\Itineraire;
use App\Form\ItineraireType;
use App\Repository\ItineraireRepository;
use App\Repository\StationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/itineraire')]
class ItineraireController extends AbstractController
{
    #[Route('/{_locale}/iti', name: 'itineraire_index', requirements: ['_locale' => 'fr|en'], defaults: ['_locale' => 'fr'], methods: ['GET'])]

    public function index(ItineraireRepository $itineraireRepository, Request $request): Response
    {
        $searchTerm = $request->query->get('search');

        if ($searchTerm) {
            $itineraires = $itineraireRepository->searchByName($searchTerm);
        } else {
            $itineraires = $itineraireRepository->findAll();
        }
        return $this->render('back/itineraire/index.html.twig', [
            'itineraires' => $itineraires,
        ]);
    }
    #[Route('/{_locale}/iti/{id}', name: 'itineraire_show', requirements: ['_locale' => 'fr|en'], defaults: ['_locale' => 'fr'], methods: ['GET'])]
    public function show(Itineraire $itineraire): Response
    {
        return $this->render('back/itineraire/show.html.twig', [
            'itineraire' => $itineraire,
        ]);
    }
    
    #[Route('/{_locale}/itineraire/new', name: 'itineraire_new', requirements: ['_locale' => 'fr|en'], defaults: ['_locale' => 'fr'], methods: ['GET', 'POST'])]

    public function new(Request $request, EntityManagerInterface $entityManager, StationRepository $stationRepository): Response
    {
        $stations = $stationRepository->findAll();
        $stationCoords = [];

        foreach ($stations as $station) {
            $stationCoords[$station->getId()] = [
                'latitude' => $station->getLatitude(),
                'longitude' => $station->getLongitude(),
            ];
        }


        $itineraire = new Itineraire();
        $form = $this->createForm(ItineraireType::class, $itineraire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($itineraire);
            $entityManager->flush();

            return $this->redirectToRoute('itineraire_index');
        }

        return $this->render('back/itineraire/new.html.twig', [
            'form' => $form,
            'stations' => $stationCoords

        ]);
    }
    
    #[Route('/{_locale}/{id}/edit', name: 'itineraire_edit', requirements: ['_locale' => 'fr|en'], defaults: ['_locale' => 'fr'], methods: ['GET', 'POST'])]

    public function edit(Request $request, Itineraire $itineraire, EntityManagerInterface $entityManager, StationRepository $stationRepository): Response
    {
        $stations = $stationRepository->findAll();
        $stationCoords = [];

        foreach ($stations as $station) {
            $stationCoords[$station->getId()] = [
                'latitude' => $station->getLatitude(),
                'longitude' => $station->getLongitude(),
            ];
        }

        $form = $this->createForm(ItineraireType::class, $itineraire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('itineraire_index');
        }

        return $this->render('back/itineraire/edit.html.twig', [
            'itineraire' => $itineraire,
            'form' => $form,
            'stations' => $stationCoords

        ]);
    }
    #[Route('/{_locale}/{id}/delete', name: 'itineraire_delete', requirements: ['_locale' => 'fr|en'], defaults: ['_locale' => 'fr'])]

    public function delete(Request $request, Itineraire $itineraire, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $entityManager->remove($itineraire);
            $entityManager->flush();

            return $this->redirectToRoute('itineraire_index');
        }

        return $this->render('back/itineraire/delete.html.twig', [
            'itineraire' => $itineraire,
        ]);
    }
}
