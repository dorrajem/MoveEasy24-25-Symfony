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
use Endroid\QrCode\Builder\Builder;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Builder\BuilderRegistryInterface;
use Endroid\QrCodeBundle\Response\QrCodeResponse;

use Symfony\Component\HttpFoundation\JsonResponse;

use Endroid\QrCode\Writer\PngWriter; 



use App\Data\SearchData;
use App\Form\SearchFormType;


class EquipementController extends AbstractController
{
   
    
    #[Route('/equipement', name: 'list_equipements', methods: ['GET'])]
public function list(Request $request, EquipementRepository $equipementRepository): Response
{
    $searchData = new SearchData();
    $form = $this->createForm(SearchFormType::class, $searchData);
    $form->handleRequest($request);

    // Si les données de recherche sont vides, récupérer tous les équipements
    if (empty($searchData->type) && empty($searchData->statut)) {
        $equipements = $equipementRepository->findAll();
    } else {
        // Sinon, rechercher les équipements en fonction des critères
        $equipements = $equipementRepository->findBySearch($searchData);
    }

    // Calculer la santé pour chaque équipement
    foreach ($equipements as $equipement) {
        $equipement->sante = $this->calculerSanteEquipement($equipement);
    }

    return $this->render('equipement/showEq.html.twig', [
        'equipement' => $equipements,
        'form' => $form->createView(),
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


// Fonction pour calculer le score de santé selon les nouvelles règles
private function calculerSanteEquipement(Equipement $equipement): int
{
    $sante = 100; // Score de départ

    // Règle 1: Quantité disponible faible
    if ($equipement->getQuantiteDisponible() < 2) {
        $sante -= 30; // Soustraire 30 points si la quantité disponible est inférieure à 5
    }

    // Règle 2: Nombre de maintenances
    $maintenances = $equipement->getMaintenances();
    if (count($maintenances) >= 3) {
        $sante -= 20; // Soustraire 20 points si le nombre de maintenances est supérieur ou égal à 5
    }

    // Règle 3: Dernière maintenance trop ancienne (plus de 6 mois)
    foreach ($maintenances as $maintenance) {
        $dateMaintenance = $maintenance->getDateMaintenance();
        if ($dateMaintenance && $dateMaintenance < new \DateTime('-6 months')) {
            $sante -= 25; // Soustraire 25 points si la dernière maintenance est trop ancienne
            break; // Dès qu'on trouve une maintenance ancienne, on applique la pénalité
        }
    }

    // S'assurer que le score de santé reste entre 0 et 100
    if ($sante > 100) {
        $sante = 100;
    } elseif ($sante < 0) {
        $sante = 0;
    }

    return $sante;
}
















}





