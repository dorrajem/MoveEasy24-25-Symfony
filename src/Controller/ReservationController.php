<?php

namespace App\Controller;

use App\Entity\Reservation;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Entity\Paiement;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use App\Repository\PaiementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/reservation')]
class ReservationController extends AbstractController
{
    #[Route('/search', name: 'app_reservation_search', methods: ['GET'])]
    public function search(Request $request, ReservationRepository $reservationRepository): Response
    {
        // Important: check if it's an AJAX request
     
    
        $keyword = $request->query->get('q', '');
    
        $results = $reservationRepository->searchByKeyword($keyword);
    
        return $this->render('back/reservation/_table_body.html.twig', [
            'reservations' => $results,
        ]);
    }
    

    #[Route('/', name: 'app_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository): Response
    {
        $reservations = $reservationRepository->findAll();

        return $this->render('back/reservation/show.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    
    #[Route('/{id}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Reservation $reservation,
        EntityManagerInterface $entityManager,
        \Symfony\Component\Mailer\MailerInterface $mailer
    ): Response {
        $payment = $reservation->getPaiement();
    
        if ($reservation->getEtatR() === null) {
            $reservation->setEtatR('En attente');
        }
    
        $originalStatus = $reservation->getEtatR();
    
        $form = $this->createForm(ReservationType::class, $reservation, [
            'only_status' => true
        ]);
    
        $originalData = [
            'dateR' => $reservation->getDateR(),
            'heureR' => $reservation->getHeureR(),
            'nbrplace' => $reservation->getNbrplace(),
            'typeplace' => $reservation->getTypeplace(),
            'typeTrainR' => $reservation->getTypeTrainR(),
            'destinationR' => $reservation->getDestinationR(),
            'NameUser' => $reservation->getNameUser()
        ];
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Reset original fields to protect them
            $reservation->setDateR($originalData['dateR']);
            $reservation->setHeureR($originalData['heureR']);
            $reservation->setNbrplace($originalData['nbrplace']);
            $reservation->setTypeplace($originalData['typeplace']);
            $reservation->setTypeTrainR($originalData['typeTrainR']);
            $reservation->setDestinationR($originalData['destinationR']);
            $reservation->setNameUser($originalData['NameUser']);
    
            if ($reservation->getEtatR() === null) {
                $reservation->setEtatR('En attente');
            }
    
            if ($reservation->getEtatR() === 'Confirmée' && $originalStatus !== 'Confirmée') {
                if (!$payment || $payment->getStatutP() !== 'Effectué') {
                    $this->addFlash('backoffice_error', 'La réservation ne peut pas être confirmée car le paiement n\'est pas effectué.');
                    $reservation->setEtatR($originalStatus);
                } else {
                    $this->addFlash('backoffice_success', 'Réservation confirmée avec succès !');
    
                    // ✅ Send enhanced bilingual confirmation email
                    $email = (new \Symfony\Component\Mime\Email())
                        ->from('professional@moveEasy.com')
                        ->to('linaboufaiedpro@gmail.com') // Change if you want dynamic user email
                        ->subject('🚆 Nouvelle réservation confirmée | New Reservation Confirmed')
                        ->html('
                            <div style="font-family: Arial, sans-serif; color: #333; background-color: #f9f9f9; padding: 20px; border-radius: 8px;">
                                <h2 style="color: #00796b;">🚆 Nouvelle réservation confirmée</h2>
                                <p>Une réservation a été confirmée avec succès :</p>
                                <ul style="list-style: none; padding: 0;">
                                    <li><strong>Destination :</strong> ' . htmlspecialchars($reservation->getDestinationR()) . '</li>
                                    <li><strong>Type de train :</strong> ' . htmlspecialchars($reservation->getTypeTrainR()) . '</li>
                                    <li><strong>Utilisateur :</strong> ' . htmlspecialchars($reservation->getNameUser()) . '</li>
                                    <li><strong>Nombre de places :</strong> ' . htmlspecialchars($reservation->getNbrplace()) . '</li>
                                </ul>
                                <p style="margin-top: 20px;">Merci pour votre confiance !</p>
    
                                <hr style="margin: 30px 0;">
    
                                <h2 style="color: #00796b;">🚆 New Reservation Confirmed</h2>
                                <p>A reservation has been successfully confirmed:</p>
                                <ul style="list-style: none; padding: 0;">
                                    <li><strong>Destination:</strong> ' . htmlspecialchars($reservation->getDestinationR()) . '</li>
                                    <li><strong>Train type:</strong> ' . htmlspecialchars($reservation->getTypeTrainR()) . '</li>
                                    <li><strong>User:</strong> ' . htmlspecialchars($reservation->getNameUser()) . '</li>
                                    <li><strong>Number of seats:</strong> ' . htmlspecialchars($reservation->getNbrplace()) . '</li>
                                </ul>
                                <p style="margin-top: 20px;">Thank you for your trust!</p>
    
                                <p style="font-size: 0.8rem; color: #888; margin-top: 30px;">
                                    &copy; ' . date('Y') . ' MoveEasy. Tous droits réservés.
                                </p>
                            </div>
                        ');
    
                    $mailer->send($email);
                }
            } else {
                $this->addFlash('backoffice_success', 'Réservation mise à jour avec succès !');
            }
    
            $entityManager->flush();
            return $this->redirectToRoute('app_reservation_index');
        }
    
        return $this->render('back/reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
            'payment' => $payment,
        ]);
    }
    

    #[Route('/{id}/payment', name: 'app_reservation_payment', methods: ['GET', 'POST'])]
    public function payment(Reservation $reservation, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($reservation->getEtatR() === null) {
            $reservation->setEtatR('En attente');
            $entityManager->persist($reservation);
        }

        $payment = $reservation->getPaiement();

        if (!$payment) {
            $payment = new Paiement();
            $payment->setReservation($reservation);
            $payment->setMethodeP('Carte Bancaire');
            $payment->setStatutP('En attente');
        }

        if ($request->isMethod('POST')) {
            $statutP = $request->request->get('statutP');
            $methodeP = $request->request->get('methodeP');

            if ($methodeP) {
                $payment->setMethodeP($methodeP);
            }

            if ($statutP) {
                $payment->setStatutP($statutP);

                if ($statutP === 'Effectué' && $reservation->getEtatR() === 'En attente') {
                    $reservation->setEtatR('Confirmée');
                    $entityManager->persist($reservation);
                    $this->addFlash('backoffice_success', 'Réservation automatiquement confirmée suite au paiement !');
                }
            }

            $entityManager->persist($payment);
            $entityManager->flush();

            $this->addFlash('backoffice_success', 'Statut de paiement mis à jour avec succès !');
            return $this->redirectToRoute('app_reservation_payment', ['id' => $reservation->getIdR()]);
        }

        return $this->render('back/reservation/payment.html.twig', [
            'reservation' => $reservation,
            'payment' => $payment,
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reservation->getIdR(), $request->request->get('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();
            $this->addFlash('backoffice_success', 'Réservation supprimée avec succès !');
        } else {
            $this->addFlash('backoffice_error', 'Token CSRF invalide, suppression annulée.');
        }

        return $this->redirectToRoute('app_reservation_index');
    }
    #[Route('/dashboard', name: 'app_reservation_dashboard', methods: ['GET'])]
    public function dashboard(ReservationRepository $reservationRepository): Response
    {
        $reservations = $reservationRepository->findAll();

        $stats = [
            'En attente' => 0,
            'Confirmée' => 0,
            'Annulée' => 0,
        ];
        $dateCounts = [];
        $trainTypes = [];
        $uniqueUsers = [];

        foreach ($reservations as $r) {
            $etat = $r->getEtatR();
            $stats[$etat] = ($stats[$etat] ?? 0) + 1;

            $date = $r->getDateR()?->format('Y-m-d');
            $dateCounts[$date] = ($dateCounts[$date] ?? 0) + 1;

            $train = $r->getTypeTrainR();
            $trainTypes[$train] = ($trainTypes[$train] ?? 0) + 1;

            $user = $r->getNameUser();
            if ($user) {
                $uniqueUsers[$user] = true;
            }
        }

        $total = count($reservations);
        $confirmed = $stats['Confirmée'] ?? 0;
        $cancelled = $stats['Annulée'] ?? 0;
        $totalUsers = count($uniqueUsers);

        return $this->render('back/reservation/dashboard.html.twig', [
            'reservations' => $reservations,
            'stats' => $stats,
            'dateCounts' => $dateCounts,
            'trainTypes' => $trainTypes,
            'totalReservations' => $total,
            'totalUsers' => $totalUsers,
            'confirmedPercent' => $total > 0 ? round(($confirmed / $total) * 100, 2) : 0,
            'cancelledPercent' => $total > 0 ? round(($cancelled / $total) * 100, 2) : 0,
        ]);
    }
}
