<?php

namespace App\Controller;

use App\Entity\Station;
use App\Form\StationType;
use App\Repository\StationRepository;
use App\Repository\ItineraireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/station')]
final class StationController extends AbstractController
{
    #[Route(name: 'station_index', methods: ['GET'])]
    public function index(StationRepository $stationRepository): Response
    {
        return $this->render('back/station/index.html.twig', [
            'stations' => $stationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'station_new', methods: ['GET', 'POST'])]
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

    #[Route('/{id}', name: 'station_show', methods: ['GET'])]
    public function show(Station $station): Response
    {
        return $this->render('back/station/show.html.twig', [
            'station' => $station,
        ]);
    }

    #[Route('/{id}/edit', name: 'station_edit', methods: ['GET', 'POST'])]
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

    #[Route('/{id}', name: 'station_delete', methods: ['POST'])]
    public function delete(Request $request, Station $station, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$station->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($station);
            $entityManager->flush();
            return $this->redirectToRoute('station_index', parameters: [], status: Response::HTTP_SEE_OTHER);

        }

        
        return $this->render('back/station/delete.html.twig', [
            'station' => $station,]);
    }
    #[Route('/station/ajouter/{id}', name: 'station_add_to_itineraire')]
public function addToItineraire(
    int $id,
    Request $request,
    EntityManagerInterface $em,
    StationRepository $stationRepo,
    ItineraireRepository $itineraireRepo
): Response {
    $itineraire = $itineraireRepo->find($id);

    if (!$itineraire) {
        throw $this->createNotFoundException('Itinéraire non trouvé');
    }

    $station = new Station();
    $station->setidItineraire($itineraire);

    $form = $this->createForm(StationType::class, $station);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->persist($station);
        $em->flush();

        $this->addFlash('success', 'Station ajoutée avec succès à l\'itinéraire!');
        return $this->redirectToRoute('itineraire_show', ['id' => $itineraire->getId()]);
    }

    return $this->render('station/new.html.twig', [
        'form' => $form->createView(),
        'itineraire' => $itineraire
    ]);
}

}
