<?php

namespace App\Controller;

use App\Entity\Train;
use App\Form\TrainType;
use App\Repository\MaintenanceTrainRepository;
use App\Repository\TrainRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;
use Twig\Environment;
use Dompdf\Dompdf;
use Dompdf\Options;

#[Route('/admin/train')]
class TrainController extends AbstractController
{
    #[Route('', name: 'app_train_index', methods: ['GET'])]
public function index(Request $request, TrainRepository $trainRepository, PaginatorInterface $paginator): Response
{
    $query = $trainRepository->createQueryBuilder('t')->orderBy('t.idTrain', 'DESC')->getQuery();

    $trains = $paginator->paginate(
        $query, // Doctrine query, not result
        $request->query->getInt('page', 1), // Current page number
        2// Limit per page
    );

    return $this->render('train/index.html.twig', [
        'trains' => $trains,
    ]);
}
#[Route('/dashboard', name: 'app_train_dashboard', methods: ['GET'])]
public function dashboard(TrainRepository $trainRepository): Response
{
    $totalTrains = $trainRepository->count([]);
    $enService = $trainRepository->count(['statut' => 'En service']);
    $horsService = $trainRepository->count(['statut' => 'Hors service']);
    $enMaintenance = $trainRepository->count(['statut' => 'En maintenance']);

    $latestTrains = $trainRepository->createQueryBuilder('t')
        ->orderBy('t.idTrain', 'DESC')
        ->setMaxResults(5)
        ->getQuery()
        ->getResult();

    return $this->render('train/dashboard.html.twig', [
        'totalTrains' => $totalTrains,
        'enService' => $enService,
        'horsService' => $horsService,
        'enMaintenance' => $enMaintenance,
        'latestTrains' => $latestTrains,
    ]);
}
#[Route('/admin/train/dashboard/pdf', name: 'app_train_dashboard_pdf')]
public function exportDashboardPdf(
    TrainRepository $trainRepo,
    Environment $twig
): Response {
    $enService = $trainRepo->count(['statut' => 'En service']);
    $enMaintenance = $trainRepo->count(['statut' => 'En maintenance']);
    $horsService = $trainRepo->count(['statut' => 'Hors service']);
    $latestTrains = $trainRepo->findBy([], ['idTrain' => 'DESC'], 5);

    $html = $twig->render('train/pdf_dashboard.html.twig', [
        'enService' => $enService,
        'enMaintenance' => $enMaintenance,
        'horsService' => $horsService,
        'latestTrains' => $latestTrains
    ]);

    $dompdf = new \Dompdf\Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    return new Response($dompdf->output(), 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="dashboard-train.pdf"',
    ]);
}



    #[Route('/new', name: 'app_train_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $train = new Train();
        $form = $this->createForm(TrainType::class, $train);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($train);
            $em->flush();

            return $this->redirectToRoute('app_train_index');
        }

        return $this->render('train/new.html.twig', [
            'train' => $train,
            'form' => $form,
        ]);
    }

    #[Route('/{idTrain}', name: 'app_train_show', methods: ['GET'])]
    public function show(Train $train): Response
    {
        return $this->render('train/show.html.twig', [
            'train' => $train,
        ]);
    }

    #[Route('/{idTrain}/edit', name: 'app_train_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Train $train, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(TrainType::class, $train);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('app_train_index');
        }

        return $this->render('train/edit.html.twig', [
            'train' => $train,
            'form' => $form,
        ]);
    }

    #[Route('/{idTrain}', name: 'app_train_delete', methods: ['POST'])]
    public function delete(Request $request, Train $train, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$train->getIdTrain(), $request->request->get('_token'))) {
            $em->remove($train);
            $em->flush();
        }

        return $this->redirectToRoute('app_train_index');
    }
}
