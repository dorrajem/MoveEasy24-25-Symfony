<?php

namespace App\Controller;
use Symfony\Component\Form\FormError;

use App\Entity\Reservation;
use App\Entity\Utilisateur;
use App\Entity\Paiement;
use App\Form\FrontReservationType;
use App\Repository\ReservationRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/reservation')]
class FrontReservationController extends AbstractController
{
    // Keep the index method unchanged
    #[Route('/', name: 'app_front_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository): Response
    {
        // For now, using a static user ID of 1
        $reservations = $reservationRepository->findBy(['iduser' => 1]);
         
        return $this->render('front/reservation/index.html.twig', [
            'reservations' => $reservations,
        ]);
    }
    
    #[Route('/new', name: 'app_front_reservation_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager, ReservationRepository $reservationRepository, UtilisateurRepository $utilisateurRepository): Response
{
    // Get user with ID 1
    $user = $utilisateurRepository->find(1);
    
    if (!$user) {
        $this->addFlash('frontoffice_error', 'Utilisateur non trouvé.');
        return $this->redirectToRoute('app_front_reservation_index');
    }
    
    $reservation = new Reservation();
    // Setting static values for now
    $reservation->setIduser(1);
    $reservation->setIdTrain(1);
    $reservation->setIdItiner(1);
    // Set default status to "En_attente"
    $reservation->setEtatR('En attente');
    // Set the user's name automatically
    $reservation->setNameUser($user->getNom());
    // Set default date and time
$reservation->setDateR(new \DateTime());
$reservation->setHeureR(new \DateTime());

    // Create form
    $form = $this->createForm(FrontReservationType::class, $reservation);
    
    // Handle the request
    $form->handleRequest($request);
    
    // Check if the form was submitted
    if ($form->isSubmitted()) {
        // Get data from the form
        $dateR = $form->get('dateR')->getData();
        $heureR = $form->get('heureR')->getData();
        
        // Add custom form errors
        $formIsValid = true;
        
        if ($dateR === null) {
            $form->get('dateR')->addError(new FormError("La date est obligatoire"));
            $formIsValid = false;
        }
        
        if ($heureR === null) {
            $form->get('heureR')->addError(new FormError("L'heure est obligatoire"));
            $formIsValid = false;
        }
        
        // Check if the form is valid after our custom validations
        if ($form->isValid() && $formIsValid) {
            // Form is valid, check available seats
            $trainId = $reservation->getIdTrain();
            $date = $reservation->getDateR();
            $time = $reservation->getHeureR();
            
            // Assume each train has a max capacity of 100 seats
            $maxCapacity = 100;
            
            // Get total reserved seats
            $reservedSeats = $reservationRepository->countReservedSeats($trainId, $date, $time);
            
            // Calculate remaining seats
            $remainingSeats = $maxCapacity - $reservedSeats;
            
            // Check if there are enough seats available
            if ($remainingSeats < $reservation->getNbrplace()) {
                $form->get('nbrplace')->addError(new FormError("Il ne reste que {$remainingSeats} places disponibles pour ce train à cette date et heure."));
            } else {
                // All checks passed - persist and flush
                $entityManager->persist($reservation);
                $entityManager->flush();
                
                // Create the payment record
                $paiement = new Paiement();
                $paiement->setReservation($reservation);
                $paiement->setMethodeP($form->get('methodeP')->getData());
                $paiement->setStatutP('En attente');
                
                $entityManager->persist($paiement);
                $entityManager->flush();
                
                $this->addFlash('frontoffice_success', 'Votre réservation a été créée avec succès!');
                return $this->redirectToRoute('app_front_reservation_index');
            }
        }
    }
    
    return $this->render('front/reservation/new.html.twig', [
        'reservation' => $reservation,
        'form' => $form->createView(),
        'seats_error' => null
    ]);
}
    #[Route('/{id}', name: 'app_front_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        // For security, check if the reservation belongs to the current user (static ID 1 for now)
        if ($reservation->getIduser() != 1) {
            $this->addFlash('frontoffice_error', 'Vous n\'êtes pas autorisé à voir cette réservation.');
            return $this->redirectToRoute('app_front_reservation_index');
        }
        
        // Fetch payment information
        $paiement = $reservation->getPaiement();
        
        return $this->render('front/reservation/show.html.twig', [
            'reservation' => $reservation,
            'paiement' => $paiement,
        ]);
    }
}