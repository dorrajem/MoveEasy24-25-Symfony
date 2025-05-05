<?php

namespace App\Controller;

use App\Entity\MaintenanceTrain;
use App\Entity\Train;
use App\Form\MaintenanceTrainType;
use App\Repository\MaintenanceTrainRepository;
use App\Repository\TrainRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/maintenance/train')]
class MaintenanceTrainController extends AbstractController
{
    #[Route('/', name: 'app_maintenance_train_index', methods: ['GET'])]
public function index(
    MaintenanceTrainRepository $maintenanceTrainRepository,
    Request $request,
    PaginatorInterface $paginator
): Response {
    // Build a query for pagination
    $query = $maintenanceTrainRepository->createQueryBuilder('m')
        ->orderBy('m.dateMaintenance', 'DESC')
        ->getQuery();

    // Paginate results
    $pagination = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1), // current page number
        4 // limit per page
    );

    return $this->render('maintenance/index.html.twig', [
        'maintenance_trains' => $pagination,
    ]);
}

#[Route('/train/{idTrain}', name: 'app_maintenance_train_by_train', methods: ['GET'])]
public function indexByTrain(
    Train $train,
    MaintenanceTrainRepository $maintenanceTrainRepository,
    Request $request,
    PaginatorInterface $paginator
): Response {
    $query = $maintenanceTrainRepository->createQueryBuilder('m')
        ->where('m.train = :train')
        ->setParameter('train', $train)
        ->orderBy('m.dateMaintenance', 'DESC')
        ->getQuery();

    $pagination = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1),
        4
    );

    return $this->render('maintenance/index.html.twig', [
        'maintenance_trains' => $pagination,
        'train' => $train,
    ]);
}

    #[Route('/new/{idTrain}', name: 'app_maintenance_train_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Train $train): Response
    {
        // Vérifier le statut du train pour déterminer les options de statut disponibles
        $statusOptions = $this->getAvailableStatusOptions($train->getStatut());
        
        if (empty($statusOptions)) {
            $this->addFlash('error', 'Le train avec ce statut ne peut pas avoir de maintenance.');
            return $this->redirectToRoute('app_train_index');
        }
    
        $maintenanceTrain = new MaintenanceTrain();
        $maintenanceTrain->setTrain($train);
    
        $form = $this->createForm(MaintenanceTrainType::class, $maintenanceTrain, [
            'status_options' => $statusOptions,
            'train_predefined' => true, // 🔥 This hides the dropdown from the form
        ]);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($maintenanceTrain);
            $entityManager->flush();
    
            // Mettre à jour le statut du train si nécessaire
            if ($maintenanceTrain->getStatut() === 'En cours') {
                $train->setStatut('En maintenance');
                $entityManager->flush();
            }
    
            return $this->redirectToRoute('app_maintenance_train_by_train', ['idTrain' => $train->getIdTrain()], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('maintenance/new.html.twig', [
            'maintenance' => $maintenanceTrain,
            'form' => $form,
            'train' => $train,
        ]);
    }
    
    #[Route('/{idMaintenance}', name: 'app_maintenance_train_show', methods: ['GET'])]
    public function show(MaintenanceTrain $maintenanceTrain): Response
    {
        return $this->render('maintenance/show.html.twig', [
            'maintenance' => $maintenanceTrain,
        ]);
    }

    #[Route('/{idMaintenance}/edit', name: 'app_maintenance_train_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MaintenanceTrain $maintenanceTrain, EntityManagerInterface $entityManager): Response
    {
        $train = $maintenanceTrain->getTrain();
        $oldStatus = $maintenanceTrain->getStatut();
        
        // Déterminer les options de statut disponibles
        $statusOptions = $this->getAvailableStatusOptions($train->getStatut());
        
        $form = $this->createForm(MaintenanceTrainType::class, $maintenanceTrain, [
            'status_options' => $statusOptions
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            // Mettre à jour le statut du train si nécessaire
            $newStatus = $maintenanceTrain->getStatut();
            if ($oldStatus !== $newStatus) {
                if ($newStatus === 'En cours') {
                    $train->setStatut('En maintenance');
                } elseif ($newStatus === 'Terminée' && $train->getStatut() === 'En maintenance') {
                    $train->setStatut('En service');
                }
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_maintenance_train_by_train', ['idTrain' => $train->getIdTrain()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('maintenance/edit.html.twig', [
            'maintenance' => $maintenanceTrain,
            'form' => $form,
        ]);
    }

    #[Route('/{idMaintenance}', name: 'app_maintenance_train_delete', methods: ['POST'])]
    public function delete(Request $request, MaintenanceTrain $maintenanceTrain, EntityManagerInterface $entityManager): Response
    {
        $train = $maintenanceTrain->getTrain();
        $trainId = $train->getIdTrain();
        
        if ($this->isCsrfTokenValid('delete'.$maintenanceTrain->getIdMaintenance(), $request->request->get('_token'))) {
            // Vérifier si cette maintenance est en cours et ajuster le statut du train si nécessaire
            if ($maintenanceTrain->getStatut() === 'En cours') {
                // Vérifier s'il reste d'autres maintenances en cours pour ce train
                $otherMaintenances = $entityManager->getRepository(MaintenanceTrain::class)->findBy([
                    'train' => $train,
                    'statut' => 'En cours'
                ]);
                
                if (count($otherMaintenances) <= 1) { // <= 1 car l'actuelle est toujours comptée
                    $train->setStatut('En service');
                    $entityManager->persist($train);
                }
            }
            
            $entityManager->remove($maintenanceTrain);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_maintenance_train_by_train', ['idTrain' => $trainId], Response::HTTP_SEE_OTHER);
    }
    
    /**
     * Détermine les options de statut disponibles selon le statut du train
     */
    private function getAvailableStatusOptions(string $trainStatus): array
    {
        switch ($trainStatus) {
            case 'Hors service':
                return ['Planifiée' => 'Planifiée', 'En cours' => 'En cours'];
            case 'En service':
                return ['Planifiée' => 'Planifiée', 'Terminée' => 'Terminée'];
            case 'En maintenance':
                return ['En cours' => 'En cours', 'Planifiée' => 'Planifiée', 'Terminée' => 'Terminée'];
            default:
                return [];
        }
    }
}