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

class MaintenanceEqController extends AbstractController
{
    #[Route('/maintenance/add', name: 'add_maintenance', methods: ['GET', 'POST'])]
    public function add(Request $request, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $maintenance = new MaintenanceEq();

        $form = $this->createForm(MaintenanceEqType::class, $maintenance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($maintenance);
            $em->flush();

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



#[Route('/maintenance/front', name: 'maintenance_front')]
public function maintenanceFront(MaintenanceEqRepository $repo): Response
{
    $maintenances = $repo->findAll();

    return $this->render('maintenance_eq/frontMaintenance.html.twig', [
        'maintenances' => $maintenances,
    ]);
}




}

