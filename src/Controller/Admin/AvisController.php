<?php

namespace App\Controller\Admin;

use App\Entity\Avis;
use App\Repository\AvisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/admin/avis', name: 'admin_avis_')]
class AvisController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(AvisRepository $avisRepository): Response
    {
        $avis = $avisRepository->findAll();
        
        // Calculer les statistiques
        $nombreAvis = count($avis);
        $noteMoyenne = 0;
        $distribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        
        if ($nombreAvis > 0) {
            $sommeNotes = 0;
            foreach ($avis as $a) {
                $note = $a->getNote();
                $sommeNotes += $note;
                if (isset($distribution[$note])) {
                    $distribution[$note]++;
                }
            }
            $noteMoyenne = $sommeNotes / $nombreAvis;
        }
        
        return $this->render('admin/avis/index.html.twig', [
            'avis' => $avis,
            'nombreAvis' => $nombreAvis,
            'noteMoyenne' => $noteMoyenne,
            'distribution' => $distribution,
        ]);
    }
    
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Avis $avis): Response
    {
        return $this->render('admin/avis/show.html.twig', [
            'avis' => $avis,
        ]);
    }
    
    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Avis $avis, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$avis->getId(), $request->request->get('_token'))) {
            $entityManager->remove($avis);
            $entityManager->flush();
            $this->addFlash('success', 'L\'avis a été supprimé.');
        }
        
        return $this->redirectToRoute('admin_avis_index');
    }
}