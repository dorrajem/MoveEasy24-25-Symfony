<?php
// src/Controller/ReclamationController.php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/reclamations')]
class ReclamationController extends AbstractController
{
    #[Route('/', name: 'app_reclamation_index', methods: ['GET'])]
    public function index(ReclamationRepository $reclamationRepository): Response
    {
        $reclamations = $reclamationRepository->findBy([], ['date_soumission' => 'DESC']); // Changed to order by date

        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }

    #[Route('/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reclamation = new Reclamation();

        // Handle the user ID directly (since you're using an integer for the user ID)
        // If no user is logged in, we set the user ID as 0 or another default value
        $userId = 0; // Default value if no user is logged in

        // If you're using session, you can fetch the user ID like this:
        // $userId = $request->getSession()->get('user_id'); // Or set a custom method to fetch it

        $reclamation->setIdUtilisateur($userId); // Set the user ID directly
        
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($reclamation);
                $entityManager->flush();
                
                $this->addFlash('success', 'Votre réclamation a été soumise avec succès!');
                return $this->redirectToRoute('app_reclamation_index');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Une erreur est survenue lors de la soumission: ' . $e->getMessage());
            }
        } elseif ($form->isSubmitted()) {
            // Form is submitted but not valid
            $this->addFlash('danger', 'Veuillez corriger les erreurs dans le formulaire.');
        }

        return $this->render('reclamation/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
        // If you want to ensure users can only see their own reclamations
        // assuming you have a User entity and authentication:
        // $currentUserId = $this->getUser()->getId(); // Or however you get the current user's ID
        // if ($reclamation->getIdUtilisateur() !== $currentUserId) {
        //     throw $this->createAccessDeniedException('Vous ne pouvez pas voir cette réclamation.');
        // }
    
        // For now, let's allow viewing any reclamation
        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        // Check access based on user ID
        //$this->checkAccess($reclamation);

        // Store original values for logging changes
        $originalData = [
            'categorie' => $reclamation->getCategorie(),
            'statut' => $reclamation->getStatut(),
            'description' => $reclamation->getDescription(),
            'reponse' => $reclamation->getReponse(),
        ];

        $form = $this->createForm(ReclamationType::class, $reclamation, [
            'is_admin' => $this->isAdmin() // Pass admin status to form
        ]);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Log changes if needed (you could store this in a changes table)
                $changes = [];
                if ($originalData['categorie'] !== $reclamation->getCategorie()) {
                    $changes['categorie'] = [
                        'old' => $originalData['categorie'],
                        'new' => $reclamation->getCategorie()
                    ];
                }
                if ($originalData['statut'] !== $reclamation->getStatut()) {
                    $changes['statut'] = [
                        'old' => $originalData['statut'],
                        'new' => $reclamation->getStatut()
                    ];
                }
                if ($originalData['description'] !== $reclamation->getDescription()) {
                    $changes['description'] = [
                        'old' => substr($originalData['description'], 0, 50) . '...',
                        'new' => substr($reclamation->getDescription(), 0, 50) . '...'
                    ];
                }
                if ($originalData['reponse'] !== $reclamation->getReponse()) {
                    $changes['reponse'] = [
                        'old' => $originalData['reponse'] ? substr($originalData['reponse'], 0, 50) . '...' : 'Non définie',
                        'new' => $reclamation->getReponse() ? substr($reclamation->getReponse(), 0, 50) . '...' : 'Non définie'
                    ];
                }
                
                // Update the entity
                $entityManager->flush();

                // You could store $changes in a separate table to track history
                
                $this->addFlash('success', 'La réclamation a été mise à jour avec succès!');
                return $this->redirectToRoute('app_reclamation_show', ['id' => $reclamation->getId()]);
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Une erreur est survenue lors de la mise à jour: ' . $e->getMessage());
            }
        } elseif ($form->isSubmitted()) {
            // Form is submitted but not valid
            $this->addFlash('danger', 'Veuillez corriger les erreurs dans le formulaire.');
        }

        return $this->render('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        // Check access based on user ID
        $this->checkAccess($reclamation);

        $token = $request->request->get('_token');
        
        if (!$this->isCsrfTokenValid('delete'.$reclamation->getId(), $token)) {
            $this->addFlash('danger', 'Token CSRF invalide. Veuillez réessayer.');
            return $this->redirectToRoute('app_reclamation_show', ['id' => $reclamation->getId()]);
        }
        
        try {
            // Save claim data for logging before deletion
            $claimData = [
                'id' => $reclamation->getId(),
                'categorie' => $reclamation->getCategorie(),
                'description' => $reclamation->getDescription(),
                'dateSoumission' => $reclamation->getDateSoumission()->format('Y-m-d H:i:s'),
                'idUtilisateur' => $reclamation->getIdUtilisateur(),
                'reponse' => $reclamation->getReponse(),
            ];
            
            // Delete the claim
            $entityManager->remove($reclamation);
            $entityManager->flush();
            
            // Log the deletion (you could store this in a separate log table)
            // $this->logDeletion($claimData);
            
            $this->addFlash('success', 'La réclamation a été supprimée avec succès!');
            
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Une erreur est survenue lors de la suppression: ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_reclamation_index');
    }

    private function checkAccess(Reclamation $reclamation): void
    {
        // For real applications, get the actual user ID from the session or security context
        $userId = $this->getUserId();
        
        // Check if the reclamation belongs to the user or if the user is an admin
        if ($reclamation->getIdUtilisateur() !== $userId && !$this->isAdmin()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette réclamation.');
        }
    }
    
    private function getUserId(): int 
    {
        // In a real application, this would get the authenticated user's ID
        // For now, we'll use a mock value
        return 0; // Default user ID (adjust based on your app's logic)
        
        // If using Symfony security:
        // return $this->getUser() ? $this->getUser()->getId() : 0;
    }
    
    private function isAdmin(): bool
    {
        // In a real application, check if user has admin role
        return true; // Adjust based on your application logic
        
        // If using Symfony security:
        // return $this->isGranted('ROLE_ADMIN');
    }
}