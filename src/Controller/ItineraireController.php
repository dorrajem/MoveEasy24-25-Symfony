<?php

namespace App\Controller;

use App\Entity\Itineraire;
use App\Form\ItineraireType;
use App\Repository\ItineraireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/itineraire')]
class ItineraireController extends AbstractController
{
    #[Route('/', name: 'itineraire_index', methods: ['GET'])]
    public function index(ItineraireRepository $itineraireRepository): Response
    {
        return $this->render('back/itineraire/index.html.twig', [
            'itineraires' => $itineraireRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'itineraire_show', methods: ['GET'])]
    public function show(Itineraire $itineraire): Response
    {
        return $this->render('back/itineraire/show.html.twig', [
            'itineraire' => $itineraire,
        ]);
    }
    
    
    #[Route('/new', name: 'itineraire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
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
        ]);
    }

    #[Route('/{id}/edit', name: 'itineraire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Itineraire $itineraire, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ItineraireType::class, $itineraire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('itineraire_index');
        }

        return $this->render('back/itineraire/edit.html.twig', [
            'itineraire' => $itineraire,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'itineraire_delete', methods: ['GET', 'POST'])]
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
