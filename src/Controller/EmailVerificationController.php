<?php
// src/Controller/EmailVerificationController.php

namespace App\Controller;

use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class EmailVerificationController extends AbstractController
{
    public function __construct(private VerifyEmailHelperInterface $verifyEmailHelper) {}

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(
        Request $request,
        UtilisateurRepository $userRepo,
        EntityManagerInterface $em
    ): Response {
        // Récupérer l'ID utilisateur depuis l'URL
        $userId = $request->query->get('userId');
        
        // Vérifier la présence de l'ID
        if (!$userId) {
            $this->addFlash('error', 'Lien de confirmation invalide (paramètres manquants).');
            return $this->redirectToRoute('front_login');
        }
        
        // Charger l'utilisateur
        $user = $userRepo->find($userId);
        if (!$user) {
            $this->addFlash('error', 'Utilisateur introuvable.');
            return $this->redirectToRoute('front_login');
        }
        
        // Vérifier si l'utilisateur est déjà vérifié
        // Modification ici : utilisation de isVerified() au lieu de getIsVerified()
        if ($user->isVerified()) {
            $this->addFlash('info', 'Votre compte est déjà vérifié. Vous pouvez vous connecter.');
            return $this->redirectToRoute('front_login');
        }
        
        // Valider la signature avec l'URI complète
        try {
            $this->verifyEmailHelper->validateEmailConfirmation(
                $request->getUri(),
                $user->getId(),
                $user->getEmail()
            );
            
            // Tout est OK → marquer l'utilisateur comme vérifié
            $user->setIsVerified(true);
            $em->flush();
            
            $this->addFlash('success', 'Votre adresse email a été confirmée. Vous pouvez maintenant vous connecter.');
        } catch (VerifyEmailExceptionInterface $e) {
            // En cas d'erreur, afficher un message plus détaillé
            $this->addFlash('error', 'Erreur de validation : ' . $e->getReason());
        }
        
        return $this->redirectToRoute('front_login');
    }
}
