<?php

namespace App\Controller;

use App\Entity\Station;
use App\Form\StationType;
use App\Repository\StationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/station')]
final class StationController extends AbstractController
{
    #[Route('/{_locale}/station', name: 'station_index', requirements: ['_locale' => 'fr|en'], defaults: ['_locale' => 'fr'], methods: ['GET'])]

    public function index(StationRepository $stationRepository, Request $request): Response
    {
        $searchTerm = $request->query->get('search');

        if ($searchTerm) {
            $stations = $stationRepository->searchByName($searchTerm);
        } else {
            $stations = $stationRepository->findAll();
        }
        return $this->render('back/station/index.html.twig', [
            'stations' => $stations,
        ]);
    }
    #[Route('/{_locale}/station/new', name: 'station_new', requirements: ['_locale' => 'fr|en'], defaults: ['_locale' => 'fr'], methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $station = new Station();
        $form = $this->createForm(StationType::class, $station);
        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($station);
            $entityManager->flush();

            return $this->redirectToRoute('station_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/station/new.html.twig', [
            'station' => $station,
            'form' => $form,
        ]);
    }
    #[Route('/{_locale}/station/{id}', name: 'station_show', requirements: ['_locale' => 'fr|en'], defaults: ['_locale' => 'fr'], methods: ['GET'])]

    public function show(Station $station): Response
    {
        return $this->render('back/station/show.html.twig', [
            'station' => $station,
        ]);
    }
    #[Route('/{_locale}/station/{id}/edit', name: 'station_edit', requirements: ['_locale' => 'fr|en'], defaults: ['_locale' => 'fr'], methods: ['GET', 'POST'])]

    public function edit(Request $request, Station $station, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(StationType::class, $station);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('station_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/station/edit.html.twig', [
            'station' => $station,
            'form' => $form,
        ]);
    }
    #[Route('/{_locale}/station/{id}/delete', name: 'station_delete', requirements: ['_locale' => 'fr|en'], defaults: ['_locale' => 'fr'])]

    public function delete(Request $request, Station $station, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $entityManager->remove($station);
            $entityManager->flush();

            return $this->redirectToRoute('station_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/station/delete.html.twig', [
            'station' => $station,
        ]);
    }



}
