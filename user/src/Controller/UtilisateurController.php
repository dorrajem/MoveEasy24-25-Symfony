<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/utilisateur')]
final class UtilisateurController extends AbstractController
{
    #[Route(name: 'app_utilisateur_index', methods: ['GET'])]
    public function index(UtilisateurRepository $utilisateurRepository): Response
    {
        return $this->render('utilisateur/index.html.twig', [
            'utilisateurs' => $utilisateurRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_utilisateur_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur, [
            'require_password' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Récupérer le mot de passe en clair
                $plainPassword = $form->get('plainPassword')->getData();
                
                // Si le mot de passe est fourni, le hasher
                if ($plainPassword) {
                    $hashedPassword = $passwordHasher->hashPassword(
                        $utilisateur,
                        $plainPassword
                    );
                    $utilisateur->setMotDePasse($hashedPassword);
                } else {
                    // Mot de passe par défaut si aucun n'est fourni
                    $defaultPassword = 'Password123!';
                    $hashedPassword = $passwordHasher->hashPassword(
                        $utilisateur,
                        $defaultPassword
                    );
                    $utilisateur->setMotDePasse($hashedPassword);
                    
                    $this->addFlash('info', 'Un mot de passe par défaut a été généré.');
                }

                $entityManager->persist($utilisateur);
                $entityManager->flush();
                
                $this->addFlash('success', 'L\'utilisateur a été créé avec succès.');
                
                return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue: ' . $e->getMessage());
            }
        }

        return $this->render('utilisateur/new.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_utilisateur_show', methods: ['GET'])]
    public function show(Utilisateur $utilisateur): Response
    {
        return $this->render('utilisateur/show.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_utilisateur_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        Utilisateur $utilisateur, 
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $form = $this->createForm(UtilisateurType::class, $utilisateur, [
            'require_password' => false,  // Mot de passe optionnel en édition
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si un nouveau mot de passe est fourni, le hasher
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $hashedPassword = $passwordHasher->hashPassword(
                    $utilisateur,
                    $plainPassword
                );
                $utilisateur->setMotDePasse($hashedPassword);
            }
            
            $entityManager->flush();
            $this->addFlash('success', 'L\'utilisateur a été mis à jour avec succès.');
            
            return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('utilisateur/edit.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_utilisateur_delete', methods: ['POST'])]
    public function delete(
        Request $request, 
        Utilisateur $utilisateur, 
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $utilisateur->getId(), $request->request->get('_token'))) {
            $entityManager->remove($utilisateur);
            $entityManager->flush();
            $this->addFlash('success', 'L\'utilisateur a été supprimé.');
        }
        
        return $this->redirectToRoute('app_utilisateur_index', [], Response::HTTP_SEE_OTHER);
    }
}