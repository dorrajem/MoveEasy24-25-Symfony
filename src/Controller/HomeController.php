<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Form\AvisType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AvisRepository;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home.html.twig');
    }

    #[Route('/reclamation', name: 'app_reclamation_index')]
    public function reclamation(): Response
    {
        $reclamations = $this->getDummyReclamations();
        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }

    #[Route('/reclamation/new', name: 'app_reclamation_new')]
    public function newReclamation(): Response
    {
        return $this->render('reclamation/new.html.twig');
    }

    #[Route('/reclamation/{id}', name: 'app_reclamation_show')]
    public function showReclamation($id): Response
    {
        $reclamations = $this->getDummyReclamations();
        $reclamation = null;

        foreach ($reclamations as $rec) {
            if ($rec->getId() === $id) {
                $reclamation = $rec;
                break;
            }
        }

        if (!$reclamation) {
            throw $this->createNotFoundException('Réclamation non trouvée');
        }

        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    // ------------------------ AVIS ROUTES ------------------------

    #[Route('/avis', name: 'app_home_avis_index')]
    public function avisIndex(AvisRepository $avisRepository): Response
    {
        $avis = $avisRepository->findAll();
        return $this->render('avis/index.html.twig', [
            'avis' => $avis,
        ]);
    }

    #[Route('/avis/new', name: 'app_home_avis_new')]
    public function avisNew(Request $request, EntityManagerInterface $entityManager): Response
    {
        $avis = new Avis();
        $avis->setDateAvis(new \DateTime());

        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($avis);
            $entityManager->flush();

            $this->addFlash('success', 'Votre avis a été ajouté avec succès !');

            return $this->redirectToRoute('app_home_avis_index');
        }

        return $this->render('avis/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/avis/{id}', name: 'app_home_avis_show')]
    public function avisShow(Avis $avis): Response
    {
        return $this->render('avis/show.html.twig', [
            'avis' => $avis,
        ]);
    }
}
