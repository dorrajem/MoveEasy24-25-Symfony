<?php

namespace App\Controller;

use App\Entity\Reservation;
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
    #[Route('/', name: 'app_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository): Response
    {
        $reservations = $reservationRepository->findAll();
        if (empty($reservations)) {
            $this->addFlash('frontoffice_warning', 'Aucune réservation trouvée dans la base de données !');
        }
        
        return $this->render('back/reservation/show.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation, EntityManagerInterface $entityManager, PaiementRepository $paiementRepository): Response
    {
        // Check if there's a payment for this reservation
        $payment = $reservation->getPaiement();
        
        // Ensure etatR is never null
        if ($reservation->getEtatR() === null) {
            $reservation->setEtatR('En attente');
        }
        
        $originalStatus = $reservation->getEtatR();
        
        // Create form with only_status option to make only the status field editable
        $form = $this->createForm(ReservationType::class, $reservation, [
            'only_status' => true
        ]);
        
        // Store original values to preserve them after form submission
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
            // Restore all original values except etatR
            $reservation->setDateR($originalData['dateR']);
            $reservation->setHeureR($originalData['heureR']);
            $reservation->setNbrplace($originalData['nbrplace']);
            $reservation->setTypeplace($originalData['typeplace']);
            $reservation->setTypeTrainR($originalData['typeTrainR']);
            $reservation->setDestinationR($originalData['destinationR']);
            $reservation->setNameUser($originalData['NameUser']);
            
            // Double-check that etatR is not null after form submission
            if ($reservation->getEtatR() === null) {
                $reservation->setEtatR('En attente');
            }
            
            $newStatus = $reservation->getEtatR();
            
            // Check if trying to set status to Confirmée
            if ($newStatus === 'Confirmée' && $originalStatus !== 'Confirmée') {
                // Check if payment exists and is completed
                if (!$payment || $payment->getStatutP() !== 'Effectué') {
                    $this->addFlash('backoffice_error', 'La réservation ne peut pas être confirmée car le paiement n\'est pas effectué.');
                    $reservation->setEtatR($originalStatus); // Revert status change
                } else {
                    $this->addFlash('backoffice_success', 'Réservation confirmée avec succès !');
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
            'payment' => $payment, // Pass payment to the template
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

    #[Route('/{id}/payment', name: 'app_reservation_payment', methods: ['GET', 'POST'])]
    public function payment(Reservation $reservation, Request $request, EntityManagerInterface $entityManager, PaiementRepository $paiementRepository): Response
    {
        // Ensure etatR is never null
        if ($reservation->getEtatR() === null) {
            $reservation->setEtatR('En attente');
            $entityManager->persist($reservation);
        }
        
        // Find existing payment or create a new one
        $payment = $reservation->getPaiement();

        if (!$payment) {
            $payment = new Paiement();
            $payment->setReservation($reservation);
            $payment->setMethodeP('Carte Bancaire'); // Default or get from form
            $payment->setStatutP('En attente'); // Default status
        }
        
        // If POST request and you're updating the payment status
        if ($request->isMethod('POST')) {
            $statutP = $request->request->get('statutP');
            $methodeP = $request->request->get('methodeP');
            
            // Update payment information
            if ($methodeP) {
                $payment->setMethodeP($methodeP);
            }
            
            if ($statutP) {
                $payment->setStatutP($statutP);
                
                // If payment is completed, automatically confirm the reservation
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
}