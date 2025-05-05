<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Utilisateur;
use App\Entity\Paiement;
use App\Form\FrontReservationType;
use App\Repository\ReservationRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/front/reservation')]
class FrontReservationController extends AbstractController
{
    #[Route('/', name: 'app_front_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository): Response
    {
        // ✅ Show all reservations instead of filtering by user
        $reservations = $reservationRepository->findAll();

        return $this->render('front/reservation/index.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    #[Route('/new', name: 'app_front_reservation_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        ReservationRepository $reservationRepository,
        UtilisateurRepository $utilisateurRepository
    ): Response {
        $user = $utilisateurRepository->find(10);

        if (!$user) {
            $this->addFlash('frontoffice_error', 'Utilisateur non trouvé.');
            return $this->redirectToRoute('app_front_reservation_index');
        }

        $reservation = new Reservation();
        $reservation->setIduser(10);
        $reservation->setIdTrain(1);
        $reservation->setIdItiner(1);
        $reservation->setEtatR('En attente');
        $reservation->setNameUser($user->getNom());
        $reservation->setDateR(new \DateTime());
        $reservation->setHeureR(new \DateTime());

        $form = $this->createForm(FrontReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $dateR = $form->get('dateR')->getData();
            $heureR = $form->get('heureR')->getData();
            $formIsValid = true;

            if ($dateR === null) {
                $form->get('dateR')->addError(new FormError("La date est obligatoire"));
                $formIsValid = false;
            }

            if ($heureR === null) {
                $form->get('heureR')->addError(new FormError("L'heure est obligatoire"));
                $formIsValid = false;
            }

            if ($form->isValid() && $formIsValid) {
                $trainId = $reservation->getIdTrain();
                $date = $reservation->getDateR();
                $time = $reservation->getHeureR();
                $maxCapacity = 100;
                $reservedSeats = $reservationRepository->countReservedSeats($trainId, $date, $time);
                $remainingSeats = $maxCapacity - $reservedSeats;

                if ($remainingSeats < $reservation->getNbrplace()) {
                    $form->get('nbrplace')->addError(new FormError("Il ne reste que {$remainingSeats} places disponibles."));
                } else {
                    $entityManager->persist($reservation);
                    $entityManager->flush();

                    $paiement = new Paiement();
                    $paiement->setReservation($reservation);
                    $paiement->setMethodeP($form->get('methodeP')->getData());
                    $paiement->setStatutP('En attente');

                    $entityManager->persist($paiement);
                    $entityManager->flush();

                    if ($paiement->getMethodeP() === 'Carte bancaire') {
                        return $this->redirectToRoute('stripe_checkout', [
                            'trainType' => $reservation->getTypeTrainR(),
                            'nbrPlaces' => $reservation->getNbrplace(),
                            'reservationId' => $reservation->getIdR(),
                        ]);
                    }

                    $this->addFlash('frontoffice_success', 'Réservation créée avec succès !');
                    return $this->redirectToRoute('app_front_reservation_index');
                }
            }
        }

        return $this->render('front/reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
            'seats_error' => null,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_front_reservation_show', methods: ['GET'])]
    public function show(int $id, ReservationRepository $reservationRepository): Response
    {
        $reservation = $reservationRepository->findOneBy(['idR' => $id]);
    
        if (!$reservation) {
            $this->addFlash('frontoffice_error', 'Réservation introuvable.');
            return $this->redirectToRoute('app_front_reservation_index');
        }
    
        return $this->render('front/reservation/show.html.twig', [
            'reservation' => $reservation,
            'paiement' => $reservation->getPaiement(),
        ]);
    }
    

    #[Route('/calendar', name: 'app_front_reservation_calendar', methods: ['GET'])]
    public function calendar(ReservationRepository $reservationRepository): Response
    {
        $reservations = $reservationRepository->findAll();
    
        $events = [];
        $statuses = [];
    
        foreach ($reservations as $reservation) {
            $status = strtolower(str_replace(' ', '_', $reservation->getEtatR()));
            $statuses[$status] = ucfirst($reservation->getEtatR());
    
            $color = match ($status) {
                'confirmée' => '#4caf50',
                'en_attente' => '#ff9800',
                'annulée', 'annulee' => '#f44336',
                default => '#2196f3',
            };
    
            $events[] = [
                'id' => $reservation->getIdR(),
                'title' => $reservation->getNameUser() . ': ' . $reservation->getDestinationR() . ' - ' . $reservation->getTypeTrainR(),
                'start' => $reservation->getDateR()->format('Y-m-d') . 'T' . $reservation->getHeureR()->format('H:i:s'),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'status' => $status,
            ];
        }
    
        return $this->render('front/reservation/calendar.html.twig', [
            'events' => json_encode($events),
            'statuses' => $statuses,
        ]);
    }
    
}
