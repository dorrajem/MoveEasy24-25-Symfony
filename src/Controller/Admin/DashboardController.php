<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ReclamationRepository;
use App\Repository\AvisRepository;

#[Route('/admin', name: 'admin_')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'dashboard')]
    public function index(ReclamationRepository $reclamationRepository, AvisRepository $avisRepository): Response
    {
        // Get all reclamations and avis
        $reclamations = $reclamationRepository->findAll();
        $avis = $avisRepository->findAll();
        
        // Calculate statistics for avis (reviews)
        $nombreAvis = count($avis);
        $noteMoyenne = 0;
        
        // Initialize distribution array for star ratings (1-5)
        $distribution = [
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0
        ];
        
        if ($nombreAvis > 0) {
            // Calculate average rating
            $sommeNotes = array_sum(array_map(function($avis) {
                return $avis->getNote();
            }, $avis));
            $noteMoyenne = $sommeNotes / $nombreAvis;
            
            // Calculate distribution of ratings
            foreach ($avis as $avisItem) {
                $note = $avisItem->getNote();
                if ($note >= 1 && $note <= 5) {
                    $distribution[$note]++;
                }
            }
        }
        
        // Statistics for reclamations (complaints)
        $reclamationsTotal = count($reclamations);
        
        $reclamationsEnAttente = count(array_filter($reclamations, function($reclamation) {
            return $reclamation->getStatut() == 'en attente';
        }));
        
        $reclamationsTraitees = count(array_filter($reclamations, function($reclamation) {
            return $reclamation->getStatut() == 'traitée';
        }));
        
        // Get recent reclamations (limit to 5)
        $reclamationsRecentes = array_slice($reclamations, 0, 5);
        
        // Sort by submission date (newest first) if available
        usort($reclamationsRecentes, function($a, $b) {
            return $b->getDateSoumission() <=> $a->getDateSoumission();
        });
        
        // Get recent avis (limit to 3)
        $avisRecents = array_slice($avis, 0, 3);
        
        // Sort by date (newest first) if available
        usort($avisRecents, function($a, $b) {
            return $b->getDateAvis() <=> $a->getDateAvis();
        });
        
        return $this->render('admin/dashboard.html.twig', [
            'nombreAvis' => $nombreAvis,
            'noteMoyenne' => $noteMoyenne,
            'distribution' => $distribution,
            'reclamationsTotal' => $reclamationsTotal,
            'reclamationsEnAttente' => $reclamationsEnAttente,
            'reclamationsTraitees' => $reclamationsTraitees,
            'reclamationsRecentes' => $reclamationsRecentes,
            'avisRecents' => $avisRecents,
        ]);
    }
}