<?php

namespace App\Controller;

use App\Entity\ProfilUtilisateur;
use App\Form\ProfilUtilisateurType;
use App\Repository\ProfilUtilisateurRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/profil')]
class ProfilUtilisateurController extends AbstractController
{
    #[Route('/', name: 'app_profil_utilisateur_index', methods: ['GET'])]
    public function index(ProfilUtilisateurRepository $profilRepo): Response
    {
        return $this->render('profil_utilisateur/index.html.twig', [
            'profils' => $profilRepo->findAll(),
        ]);
    }
    
    #[Route('/new', name: 'app_profil_utilisateur_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        UtilisateurRepository $userRepo,
        ValidatorInterface $validator
    ): Response
    {
        $profil = new ProfilUtilisateur();
        $form   = $this->createForm(ProfilUtilisateurType::class, $profil);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // 1️⃣ validation serveur
            $errors = $validator->validate($profil);
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
                return $this->renderForm('profil_utilisateur/new.html.twig', [
                    'profil' => $profil,
                    'form'   => $form,
                ]);
            }
            
            // 2️⃣ validation téléphone
            $telephone = $profil->getTelephone();
            if ($telephone !== null) {
                $phoneClean = preg_replace('/[^0-9+]/', '', $telephone);
                if (preg_match('/^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.\-]*\d{2}){4}$/', $telephone) !== 1) {
                    $this->addFlash('warning', 'Le format du numéro de téléphone semble incorrect. Il a été enregistré tel quel.');
                }
                if (strlen($phoneClean) < 10 || strlen($phoneClean) > 15) {
                    $this->addFlash('warning', 'La longueur du numéro de téléphone semble inhabituelle.');
                }
            }
            
            // 3️⃣ association utilisateur
            $userId = $request->query->get('user_id');
            if ($userId) {
                $utilisateur = $userRepo->find($userId);
                if ($utilisateur) {
                    $profil->setUtilisateur($utilisateur);
                } else {
                    $this->addFlash('error', 'Utilisateur introuvable');
                    return $this->renderForm('profil_utilisateur/new.html.twig', [
                        'profil' => $profil,
                        'form'   => $form,
                    ]);
                }
            } else {
                $this->addFlash('error', 'Un utilisateur doit être associé au profil');
                return $this->renderForm('profil_utilisateur/new.html.twig', [
                    'profil' => $profil,
                    'form'   => $form,
                ]);
            }
            
            // 4️⃣ persistance
            $em->persist($profil);
            $em->flush();
            
            // —> Evite la sérialisation de UploadedFile en session
            $profil->setPhotoProfilFile(null);
            
            $this->addFlash('success', 'Profil créé avec succès');
            return $this->redirectToRoute('app_profil_utilisateur_index', [], Response::HTTP_SEE_OTHER);
        }
        
        return $this->renderForm('profil_utilisateur/new.html.twig', [
            'profil' => $profil,
            'form'   => $form,
        ]);
    }
    
    #[Route('/{id}', name: 'app_profil_utilisateur_show', methods: ['GET'])]
    public function show(ProfilUtilisateur $profil): Response
    {
        return $this->render('profil_utilisateur/show.html.twig', [
            'profil' => $profil,
        ]);
    }
    
    #[Route('/{id}/edit', name: 'app_profil_utilisateur_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        ProfilUtilisateur $profil,
        EntityManagerInterface $em,
        ValidatorInterface $validator
    ): Response
    {
        $form = $this->createForm(ProfilUtilisateurType::class, $profil);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // 1️⃣ validation serveur
            $errors = $validator->validate($profil);
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
                return $this->renderForm('profil_utilisateur/edit.html.twig', [
                    'profil' => $profil,
                    'form'   => $form,
                ]);
            }
            
            // 2️⃣ validation téléphone
            $telephone = $profil->getTelephone();
            if ($telephone !== null) {
                if (preg_match('/^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.\-]*\d{2}){4}$/', $telephone) !== 1) {
                    $this->addFlash('warning', 'Le format du numéro de téléphone semble incorrect. Il a été enregistré tel quel.');
                }
            }
            
            // 3️⃣ flush
            $em->flush();
            
            // —> Evite la sérialisation de UploadedFile en session
            $profil->setPhotoProfilFile(null);
            
            $this->addFlash('success', 'Profil mis à jour avec succès');
            return $this->redirectToRoute('app_profil_utilisateur_index', [], Response::HTTP_SEE_OTHER);
        }
        
        return $this->renderForm('profil_utilisateur/edit.html.twig', [
            'profil' => $profil,
            'form'   => $form,
        ]);
    }
    
    #[Route('/{id}', name: 'app_profil_utilisateur_delete', methods: ['POST'])]
    public function delete(Request $request, ProfilUtilisateur $profil, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$profil->getId(), $request->request->get('_token'))) {
            $em->remove($profil);
            $em->flush();
            $this->addFlash('success', 'Profil supprimé avec succès');
        }
        
        return $this->redirectToRoute('app_profil_utilisateur_index', [], Response::HTTP_SEE_OTHER);
    }
}
