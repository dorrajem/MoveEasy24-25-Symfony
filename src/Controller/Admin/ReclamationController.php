<?php

namespace App\Controller\Admin;

use App\Entity\Reclamation;
use App\Repository\ReclamationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/admin/reclamation', name: 'admin_reclamation_')]
class ReclamationController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ReclamationRepository $reclamationRepository): Response
    {
        $reclamations = $reclamationRepository->findAll();
        
        return $this->render('admin/reclamation/index.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }
    
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
        return $this->render('admin/reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }
    
    #[Route('/{id}/repondre', name: 'repondre', methods: ['GET', 'POST'])]
    public function repondre(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $reponse = $request->request->get('reponse');
            $reclamation->setReponse($reponse);
            $reclamation->setStatut('traitée');
            
            $entityManager->persist($reclamation);
            $entityManager->flush();
            
            $this->addFlash('success', 'La réponse a été envoyée avec succès.');
            
            return $this->redirectToRoute('admin_reclamation_index');
        }
        
        return $this->render('admin/reclamation/repondre.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }
    
    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
            $this->addFlash('success', 'La réclamation a été supprimée.');
        }
        
        return $this->redirectToRoute('admin_reclamation_index');
    }
}