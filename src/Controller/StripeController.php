<?php

namespace App\Controller;

use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/stripe')]
class StripeController extends AbstractController
{
    #[Route('/checkout/{trainType}/{nbrPlaces}/{reservationId}', name: 'stripe_checkout')]
    public function checkout(string $trainType, int $nbrPlaces, int $reservationId): Response
    {
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        // Define prices in cents (Stripe expects cents)
        $priceMap = [
            'TGV' => 2000,       // 20€
            'Régional' => 1000,  // 10€
            'Express' => 800,    // 8€
        ];

        $unitAmount = $priceMap[$trainType] ?? 1000; // Fallback to 10€ if unknown

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => "Réservation Train : $trainType",
                    ],
                    'unit_amount' => $unitAmount,
                ],
                'quantity' => $nbrPlaces,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('stripe_success', ['reservationId' => $reservationId], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('stripe_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return new RedirectResponse($session->url);
    }

    #[Route('/success/{reservationId}', name: 'stripe_success')]
    public function success(int $reservationId, EntityManagerInterface $em): Response
    {
        $reservation = $em->getRepository(Reservation::class)->find($reservationId);

        if (!$reservation) {
            $this->addFlash('error', 'Réservation introuvable.');
            return $this->redirectToRoute('app_front_reservation_index');
        }

        // Update paiement status
        $paiement = $reservation->getPaiement();
        if ($paiement) {
            $paiement->setStatutP('Effectué');
        }

        // Update reservation status
        $reservation->setEtatR('Confirmée');

        $em->flush();

        $this->addFlash('success', 'Paiement réussi et réservation confirmée.');

        return $this->render('front/stripe/success.html.twig');
    }

    #[Route('/cancel', name: 'stripe_cancel')]
    public function cancel(): Response
    {
        $this->addFlash('error', 'Paiement annulé ou échoué.');
        return $this->render('front/stripe/cancel.html.twig');
    }
}
