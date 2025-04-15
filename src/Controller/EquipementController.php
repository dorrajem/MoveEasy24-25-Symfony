<?php

namespace App\Controller;

use App\Entity\Equipement;
use App\Form\EquipementType;
use App\Repository\EquipementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/admin/equipement')]
class EquipementController extends AbstractController
{
    #[Route('/', name: 'app_equipement_index', methods: ['GET'])]
    public function index(EquipementRepository $equipementRepository): Response
    {
        // Debug output - remove in production
        $equipements = $equipementRepository->findAll();
        if (empty($equipements)) {
            $this->addFlash('warning', 'Aucun équipement trouvé dans la base de données !');
        }

        return $this->render('BackOffice/equipement/show.html.twig', [
            'equipements' => $equipementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_equipement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $equipement = new Equipement();
        $form = $this->createForm(EquipementType::class, $equipement);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('imageFile')->getData();
            
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
    
                try {
                    $imageFile->move(
                        $this->getParameter('equipement_images_directory'),
                        $newFilename
                    );
                    $equipement->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image');
                }
            }
    
            $entityManager->persist($equipement);
            $entityManager->flush();
    
            $this->addFlash('success', 'Équipement créé avec succès!');
            return $this->redirectToRoute('app_equipement_index');
        }
    
        return $this->render('BackOffice/equipement/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    // Edit equipment
    #[Route('/{id}/edit', name: 'app_equipement_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Equipement $equipement,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        $form = $this->createForm(EquipementType::class, $equipement);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('imageFile')->getData();
            
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
    
                try {
                    // Delete old image if exists
                    if ($equipement->getImage()) {
                        $oldImagePath = $this->getParameter('equipement_images_directory').'/'.$equipement->getImage();
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }
    
                    // Move new file
                    $imageFile->move(
                        $this->getParameter('equipement_images_directory'),
                        $newFilename
                    );
                    
                    // Update entity with new filename
                    $equipement->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du remplacement de l\'image : '.$e->getMessage());
                    return $this->redirectToRoute('app_equipement_edit', ['id' => $equipement->getId()]);
                }
            }
    
            try {
                $entityManager->flush();
                $this->addFlash('success', 'Équipement mis à jour avec succès !');
                return $this->redirectToRoute('app_equipement_index');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la mise à jour : '.$e->getMessage());
            }
        }
    
        return $this->render('BackOffice/equipement/edit.html.twig', [
            'equipement' => $equipement,
            'form' => $form->createView(),
        ]);
    }

    // Delete equipment
    #[Route('/{id}', name: 'app_equipement_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Equipement $equipement,
        EntityManagerInterface $entityManager
    ): Response {
        // Verify CSRF token
        if ($this->isCsrfTokenValid('delete'.$equipement->getId(), $request->request->get('_token'))) {
            try {
                // Delete associated image file if exists
                if ($equipement->getImage()) {
                    $imagePath = $this->getParameter('equipement_images_directory').'/'.$equipement->getImage();
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
    
                // Remove the equipment from database
                $entityManager->remove($equipement);
                $entityManager->flush();
    
                $this->addFlash('success', 'L\'équipement a été supprimé avec succès !');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la suppression : '.$e->getMessage());
            }
        } else {
            $this->addFlash('error', 'Token CSRF invalide, suppression annulée.');
        }
    
        return $this->redirectToRoute('app_equipement_index');
    }
}