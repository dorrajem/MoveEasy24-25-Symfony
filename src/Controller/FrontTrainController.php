<?php

namespace App\Controller;

use App\Entity\Train;
use App\Repository\TrainRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('front/trains')]
class FrontTrainController extends AbstractController
{   
    #[Route('', name: 'front_train_list', methods: ['GET'])]
    public function index(Request $request, TrainRepository $trainRepository, PaginatorInterface $paginator): Response
    {
        $query = $trainRepository->createQueryBuilder('t')
            ->orderBy('t.idTrain', 'DESC')
            ->getQuery();

        $trains = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            6

        );

        return $this->render('front/train/list.html.twig', [
            'trains' => $trains
        ]);
    }

    #[Route('/{idTrain}', name: 'front_train_show', requirements: ['idTrain' => '\d+'], methods: ['GET'])]
    public function show(int $idTrain, TrainRepository $trainRepository): Response
    {
        $train = $trainRepository->find($idTrain);

        if (!$train) {
            throw $this->createNotFoundException('Train introuvable.');
        }

        return $this->render('front/train/show.html.twig', [
            'train' => $train
        ]);
    }

    #[Route('/search', name: 'front_train_search', methods: ['GET'])]
    public function search(Request $request, TrainRepository $trainRepository): Response
    {
        $query = $request->query->get('query', '');
        $trains = $trainRepository->createQueryBuilder('t')
            ->where('LOWER(t.TypeTrain) LIKE LOWER(:query)')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('t.idTrain', 'DESC')
            ->getQuery()
            ->getResult();
    
        return $this->render('front/train/_train_cards.html.twig', [
            'trains' => $trains
        ]);
    }
    #[Route('/filter', name: 'front_train_filter', methods: ['GET'])]
    public function filter(Request $request, TrainRepository $trainRepository): Response
    {
        $statut = $request->query->get('statut');
        $date = $request->query->get('date');
    
        $qb = $trainRepository->createQueryBuilder('t');
    
        if ($statut) {
            $qb->andWhere('t.statut = :statut')
               ->setParameter('statut', $statut);
        }
    
        if ($date) {
            $qb->andWhere('t.dateMiseEnService = :date')
               ->setParameter('date', new \DateTime($date));
        }
    
        $trains = $qb->orderBy('t.idTrain', 'DESC')->getQuery()->getResult();
    
        // ✅ Detect AJAX
        if ($request->isXmlHttpRequest()) {
            return $this->render('front/train/_train_cards.html.twig', [
                'trains' => $trains
            ]);
        }
    
        // 🚨 Fallback: if someone visits /filter directly in browser
        return $this->redirectToRoute('front_train_list');
    }
    
    

}
