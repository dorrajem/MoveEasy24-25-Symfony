<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Psr\Log\LoggerInterface;

#[Route('/reset-password')]
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger
    ) {
    }

    /**
     * Display & process form to request a password reset.
     */
    #[Route('', name: 'app_forgot_password_request')]
    public function request(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $email */
            $email = $form->get('email')->getData();
            $this->logger->info('Demande de réinitialisation de mot de passe pour: ' . $email);

            return $this->processSendingPasswordResetEmail($email, $mailer);
        }

        return $this->render('reset_password/request.html.twig', [
            'requestForm' => $form,
        ]);
    }

    /**
     * Confirmation page after a user has requested a password reset.
     */
    #[Route('/check-email', name: 'app_check_email')]
    public function checkEmail(): Response
    {
        // Generate a fake token if the user does not exist or someone hit this page directly.
        // This prevents exposing whether or not a user was found with the given email address or not
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
            $this->logger->info('Génération d\'un token factice pour check-email');
        } else {
            $this->logger->info('Token récupéré de la session pour check-email');
        }

        return $this->render('reset_password/check_email.html.twig', [
            'resetToken' => $resetToken,
        ]);
    }

    /**
     * Validates and process the reset URL that the user clicked in their email.
     */
    #[Route('/reset/{token}', name: 'app_reset_password')]
    public function reset(Request $request, UserPasswordHasherInterface $passwordHasher, ?string $token = null): Response
    {
        if ($token) {
            $this->logger->info('Token reçu dans l\'URL: ' . substr($token, 0, 10) . '...');
            // We store the token in session and remove it from the URL, to avoid the URL being
            // loaded in a browser and potentially leaking the token to 3rd party JavaScript.
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('app_reset_password');
        }

        $token = $this->getTokenFromSession();

        if (null === $token) {
            $this->logger->error('Aucun token trouvé dans la session');
            throw $this->createNotFoundException('Aucun token de réinitialisation trouvé dans l\'URL ou dans la session.');
        }

        try {
            /** @var Utilisateur $user */
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
            $this->logger->info('Token validé avec succès pour l\'utilisateur: ' . $user->getEmail());
        } catch (ResetPasswordExceptionInterface $e) {
            $this->logger->error('Erreur de validation du token: ' . $e->getReason());
            $this->addFlash('reset_password_error', sprintf(
                '%s - %s',
                ResetPasswordExceptionInterface::MESSAGE_PROBLEM_VALIDATE,
                $e->getReason()
            ));

            return $this->redirectToRoute('app_forgot_password_request');
        }

        // The token is valid; allow the user to change their password.
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // A password reset token should be used only once, remove it.
            $this->resetPasswordHelper->removeResetRequest($token);

            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // Encode(hash) the plain password, and set it.
            $user->setMotDePasse($passwordHasher->hashPassword($user, $plainPassword));
            $this->entityManager->flush();
            $this->logger->info('Mot de passe réinitialisé avec succès pour: ' . $user->getEmail());

            // The session is cleaned up after the password has been changed.
            $this->cleanSessionAfterReset();
            
            $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès.');

            // Rediriger vers la page de login front si c'est un utilisateur normal
            if ($user->getRoles() && in_array('ROLE_USER', $user->getRoles())) {
                return $this->redirectToRoute('front_login');
            }
            
            // Sinon, rediriger vers la page de login admin
            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form,
        ]);
    }

    private function processSendingPasswordResetEmail(string $emailFormData, MailerInterface $mailer): RedirectResponse
    {
        $user = $this->entityManager->getRepository(Utilisateur::class)->findOneBy([
            'email' => $emailFormData,
        ]);

        // Do not reveal whether a user account was found or not.
        if (!$user) {
            $this->logger->info('Aucun utilisateur trouvé pour l\'email: ' . $emailFormData);
            return $this->redirectToRoute('app_check_email');
        }

        $this->logger->info('Utilisateur trouvé pour l\'email: ' . $emailFormData);

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
            $this->logger->info('Token généré pour l\'utilisateur: ' . $emailFormData);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->logger->error('Erreur lors de la génération du token: ' . $e->getReason());
            
            // Si vous souhaitez informer l'utilisateur du problème
            $this->addFlash('reset_password_error', sprintf(
                'Erreur: %s',
                $e->getReason()
            ));

            return $this->redirectToRoute('app_check_email');
        }

        $email = (new TemplatedEmail())
            ->from(new Address('elyes.ayachi69@gmail.com', 'MoveEasy Support'))
            ->to((string) $user->getEmail())
            ->subject('Demande de réinitialisation de mot de passe')
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'resetToken' => $resetToken,
                'tokenLifetime' => $this->resetPasswordHelper->getTokenLifetime(),
            ])
        ;

        try {
            $mailer->send($email);
            $this->logger->info('Email de réinitialisation envoyé à: ' . $user->getEmail());
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Erreur lors de l\'envoi de l\'email: ' . $e->getMessage());
            $this->addFlash('reset_password_error', 'Une erreur est survenue lors de l\'envoi de l\'email.');
            return $this->redirectToRoute('app_forgot_password_request');
        }

        // Store the token object in session for retrieval in check-email route.
        $this->setTokenObjectInSession($resetToken);

        return $this->redirectToRoute('app_check_email');
    }
}