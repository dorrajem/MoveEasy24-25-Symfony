<?php

namespace App\Controller;

use App\Entity\MaintenanceEq;
use App\Form\MaintenanceEqType;
use App\Repository\MaintenanceEqRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Service\VonageSmsService;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\EquipementRepository;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;




class MaintenanceEqController extends AbstractController
{

    #[Route('/maintenance/add', name: 'add_maintenance', methods: ['GET', 'POST'])]
   
public function add(Request $request, ManagerRegistry $doctrine, VonageSmsService $vonageSmsService): Response
{
    $em = $doctrine->getManager();
    $maintenance = new MaintenanceEq();

    $form = $this->createForm(MaintenanceEqType::class, $maintenance);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->persist($maintenance);
        $em->flush();

        $dateMaintenance = $maintenance->getDateMaintenance()?->format('d/m/Y');
        $description = $maintenance->getDescription();
        $periode = $maintenance->getPeriode();
        $idEquipement = $maintenance->getIdEquipement()?->getIdEquipement();

        $message = "Une maintenance est ajoutée pour l'équipement $idEquipement le $dateMaintenance, consistant à $description. Vous avez un délai de $periode jours.";


        // Envoi du SMS
        $vonageSmsService->sendSms(
            '+21658387606', // Remplacer par un numéro test
            $message
        );

        $this->addFlash('success', 'Maintenance ajoutée avec succès.');
        return $this->redirectToRoute('list_maintenance');
    }

    return $this->render('maintenance_eq/addMaintenanceEq.html.twig', [
        'form' => $form,
    ]);
}

    #[Route('/maintenance', name: 'list_maintenance', methods: ['GET'])]
    public function list(MaintenanceEqRepository $repo): Response
    {
        $maintenances = $repo->findAll();

        return $this->render('maintenance_eq/showMaintenanceEq.html.twig', [
        'maintenances' => $maintenances,
         ]);
    }

#[Route('/maintenance/edit/{idMaintenance}', name: 'edit_maintenance', methods: ['GET', 'POST'])]
public function editM(Request $request, ManagerRegistry $doctrine, MaintenanceEqRepository $repo, int $idMaintenance): Response
{
    $maintenanceEq = $repo->findOneBy(['idMaintenance' => $idMaintenance]);

    if (!$maintenanceEq) {
        throw $this->createNotFoundException('Maintenance non trouvée.');
    }

    $form = $this->createForm(MaintenanceEqType::class, $maintenanceEq);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em = $doctrine->getManager();
        $em->flush();

        return $this->redirectToRoute('list_maintenance');
    }

    return $this->renderForm('maintenance_eq/editMaintenanceEq.html.twig', [
        'form' => $form,
    ]);
}

#[Route('/maintenance/delete/{idMaintenance}', name: 'delete_maintenance', methods: ['POST'])]
public function deleteMaintenance(Request $request, ManagerRegistry $doctrine, MaintenanceEq $maintenanceEq): Response
{
    if ($this->isCsrfTokenValid('delete' . $maintenanceEq->getIdMaintenance(), $request->request->get('_token'))) {
        $em = $doctrine->getManager();
        $em->remove($maintenanceEq);
        $em->flush();
    }

    return $this->redirectToRoute('list_maintenance');
}




#[Route('/api/equipement/{idEquipement}/maintenances', name: 'equipement_maintenance_history', methods: ['GET'])]
public function maintenanceHistory(int $idEquipement, MaintenanceEqRepository $maintenanceRepo, EquipementRepository $equipementRepo): JsonResponse
{
    $equipement = $equipementRepo->findOneBy(['idEquipement' => $idEquipement]);

    if (!$equipement) {
        return $this->json(['error' => 'Équipement introuvable.'], 404);
    }

    $maintenances = $maintenanceRepo->findBy(['idEquipement' => $equipement]);

    if (count($maintenances) == 0) {
        return $this->json(['error' => 'Aucune maintenance pour cet équipement.'], 404); // Renvoie un message approprié
    }

    $data = [];
    foreach ($maintenances as $maintenance) {
        $data[] = [
            'date' => $maintenance->getDateMaintenance()?->format('d/m/Y'),
            'description' => $maintenance->getDescription(),
            'periode' => $maintenance->getPeriode(),
        ];
    }

    return $this->json([
        'equipement' => [
            'id' => $equipement->getIdEquipement(),
            'type' => $equipement->getTypeEquipement(),
            'train' => $equipement->getIdTrain() ?? 'Train inconnu',
        ],
        'maintenances' => $data,
    ]);
}


#[Route('/equipement/{idEquipement}/historique', name: 'equipement_maintenance_historique', methods: ['GET'])]
public function historiqueMaintenance(int $idEquipement, EquipementRepository $equipementRepo, MaintenanceEqRepository $maintenanceRepo): Response
{
    $equipement = $equipementRepo->findOneBy(['idEquipement' => $idEquipement]);

    if (!$equipement) {
        return $this->render('equipement/historique.html.twig', [
            'error' => 'Équipement introuvable.'
        ]);
    }

    $maintenances = $maintenanceRepo->findBy(['idEquipement' => $equipement]);

    return $this->render('equipement/historique.html.twig', [
        'equipement' => $equipement,
        'maintenances' => $maintenances,
    ]);
}



#[Route('/equipement/{idEquipement}/qr-code', name: 'generate_qr_code')]
public function generateQrCode(int $idEquipement, EquipementRepository $equipementRepo, MaintenanceEqRepository $maintenanceRepo): Response
{
    $equipement = $equipementRepo->find($idEquipement);

    if (!$equipement) {
        throw $this->createNotFoundException('Équipement introuvable.');
    }

    $maintenances = $maintenanceRepo->findBy(['idEquipement' => $equipement]);

    if (count($maintenances) === 0) {
        return new Response('Aucune maintenance pour cet équipement.', 404);
    }

    // Utilise l'URL de la route "historique_simple"
    $data = $this->generateUrl('equipement_maintenance_historique_simple', ['idEquipement' => $idEquipement], UrlGeneratorInterface::ABSOLUTE_URL);

    $result = Builder::create()
        ->writer(new PngWriter())
        ->data($data)
        ->size(300)
        ->margin(10)
        ->build();

    return new Response($result->getString(), 200, ['Content-Type' => 'image/png']);
}


#[Route('/equipement/{idEquipement}/historique-simple', name: 'equipement_maintenance_historique_simple', methods: ['GET'])]
public function historiqueSimple(int $idEquipement, EquipementRepository $equipementRepo, MaintenanceEqRepository $maintenanceRepo): Response
{
    $equipement = $equipementRepo->findOneBy(['idEquipement' => $idEquipement]);

    if (!$equipement) {
        return $this->render('equipement/historique_simple.html.twig', [
            'error' => 'Équipement introuvable.'
        ]);
    }

    $maintenances = $maintenanceRepo->findBy(['idEquipement' => $equipement]);

    return $this->render('equipement/historique_simple.html.twig', [
        'equipement' => $equipement,
        'maintenances' => $maintenances,
    ]);
}







}

