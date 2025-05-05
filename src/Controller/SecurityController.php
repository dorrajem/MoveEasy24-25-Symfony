<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route; // ou Attribute selon votre version de Symfony
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    // Modification : la route de connexion est placée sous /utilisateur pour correspondre au firewall
    #[Route(path: '/utilisateur/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Redirection si l'utilisateur est déjà connecté
        if ($this->getUser()) {
            // Redirige vers la page de gestion des utilisateurs (vérifiez bien que 'app_utilisateur_index' existe)
            return $this->redirectToRoute('app_utilisateur_index');
        }

        // Récupère l'erreur de connexion s'il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();
        // Récupère le dernier nom d'utilisateur saisi
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    // La route de logout est également positionnée sous /utilisateur pour correspondre au pattern du firewall
    #[Route(path: '/utilisateur/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Cette méthode ne doit jamais être exécutée car Symfony interceptera la requête de logout.
        throw new \LogicException('Cette méthode ne doit pas être exécutée, elle est interceptée par le firewall.');
    }
}
