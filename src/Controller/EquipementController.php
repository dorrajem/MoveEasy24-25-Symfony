<?php

namespace App\Controller;

use App\Entity\Equipement;
use App\Form\EquipementType;
use App\Repository\EquipementRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EquipementController extends AbstractController
{
    #[Route('/equipement', name: 'list_equipements', methods: ['GET'])]
public function list(EquipementRepository $equipementRepository): Response
{
    return $this->render('equipement/showEq.html.twig', [
        'equipement' => $equipementRepository->findAll(),
    ]);
}


#[Route('/equipement/add', name: 'add_equipement', methods: ['GET', 'POST'])]
public function add(Request $request, ManagerRegistry $doctrine): Response
{
    $em = $doctrine->getManager();
    $equipement = new Equipement();
    $form = $this->createForm(EquipementType::class, $equipement);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $repo = $em->getRepository(Equipement::class);
        $existant = $repo->findOneBy([
            'typeEquipement' => $equipement->getTypeEquipement(),
            'idTrain' => $equipement->getIdTrain(),
        ]);

        if ($existant) {
            $this->addFlash('warning', 'Cet équipement est déjà enregistré pour ce train.');
        } else {
            $em->persist($equipement);
            $em->flush();
            $this->addFlash('success', 'Équipement ajouté avec succès !');
            return $this->redirectToRoute('list_equipements');
        }
    }

    return $this->renderForm('equipement/addEq.html.twig', [
        'form' => $form,
    ]);
}



    #[Route('/equipement/edit/{idEquipement}', name: 'edit_equipement', methods: ['GET', 'POST'])]
public function edit(Request $request, ManagerRegistry $doctrine, Equipement $equipement): Response
{
    $form = $this->createForm(EquipementType::class, $equipement);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em = $doctrine->getManager();
        $em->flush();

        return $this->redirectToRoute('list_equipements'); // or wherever you want to go after edit
    }

    return $this->renderForm('equipement/editEq.html.twig', [
        'form' => $form,
        'equipement' => $equipement,
    ]);
}
#[Route('/equipement/delete/{id}', name: 'delete_equipement', methods: ['POST'])]
public function delete(Request $request, Equipement $equipement, ManagerRegistry $doctrine): Response
{
    if ($this->isCsrfTokenValid('delete'.$equipement->getIdEquipement(), $request->request->get('_token'))) {
        $em = $doctrine->getManager();
        $em->remove($equipement);
        $em->flush();
    }

    return $this->redirectToRoute('list_equipements');
}
#[Route('/front/equipements', name: 'front_equipements', methods: ['GET'])]
public function frontList(EquipementRepository $equipementRepository): Response
{
    return $this->render('equipement/frontEq.html.twig', [
        'equipements' => $equipementRepository->findAll(),
    ]);
}


#[Route('/accueil', name: 'front_home')]
public function frontHome(): Response
{
    return $this->render('front_home.html.twig');
}



}
