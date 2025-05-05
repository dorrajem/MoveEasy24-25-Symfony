<?php
// src/Controller/FrontSecurityController.php
namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Pusher\Pusher;

class FrontSecurityController extends AbstractController
{
    public function __construct(
        private VerifyEmailHelperInterface $verifyEmailHelper,
        private MailerInterface $mailer,
        private Pusher $pusher
    ) {}

    /**
     * Affiche le formulaire de connexion et gère les éventuelles erreurs.
     */
    #[Route('/front/login', name: 'front_login', methods: ['GET','POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // récupère l'erreur de connexion (ou null)
        $error = $authenticationUtils->getLastAuthenticationError();
        // récupère le dernier email saisi
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('front/login.html.twig', [
            'error'         => $error,
            'last_username' => $lastUsername,
        ]);
    }

    /**
     * Inscription d'un nouvel utilisateur + envoi du mail de vérification.
     */
    #[Route('/front/signup', name: 'front_signup', methods: ['GET','POST'])]
    public function signup(Request $request, EntityManagerInterface $em): Response
    {
        $user = new Utilisateur();
        // rôle forcé côté front
        $user->setRole('Employé');

        $form = $this->createForm(UtilisateurType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // pour l'exemple, stockage en clair (vous pouvez hasher ici)
            $plain = $form->get('plainPassword')->getData();
            $user->setMotDePasse($plain);

            $em->persist($user);
            $em->flush();

            // ─── Déclenchement Pusher pour notifier le back ─────────────────
            $this->pusher->trigger(
                'notifications',           // channel
                'user-registered',         // événement
                [
                    'id'    => $user->getId(),
                    'email' => $user->getEmail(),
                    'nom'   => $user->getPrenom(),
                ]
            );
            // ────────────────────────────────────────────────────────────────

            // génère un lien signé pour la vérification de l'adresse
            $signature = $this->verifyEmailHelper->generateSignature(
                'app_verify_email',
                $user->getId(),
                $user->getEmail(),
                ['userId' => $user->getId()]
            );

            // prépare et envoie l'email
            $email = (new TemplatedEmail())
                ->from('no-reply@moveeasy.local')
                ->to($user->getEmail())
                ->subject('Confirmez votre adresse email')
                ->htmlTemplate('emails/confirmation.html.twig')
                ->context([
                    'signedUrl' => $signature->getSignedUrl(),
                    'expiresAt' => $signature->getExpiresAt(),
                ]);

            $this->mailer->send($email);

            $this->addFlash('success', 'Inscription réussie ! Un email de confirmation vous a été envoyé.');
            return $this->redirectToRoute('front_login');
        }

        return $this->render('front/signup_front.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Déconnexion (prise en charge par le firewall).
     */
    #[Route('/front/logout', name: 'front_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be empty—handled by the firewall.');
    }
}