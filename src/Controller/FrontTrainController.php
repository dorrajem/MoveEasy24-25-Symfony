<?php

namespace App\Controller;

use App\Entity\Train;
use App\Repository\TrainRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/trains')]
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
            3

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

    $qb = $trainRepository->createQueryBuilder('t');

    if (!empty($statut)) {
        $qb->where('t.statut = :statut')
           ->setParameter('statut', $statut);
    }

    $trains = $qb->orderBy('t.idTrain', 'DESC')->getQuery()->getResult();

    return $this->render('front/train/_train_cards.html.twig', [
        'trains' => $trains
    ]);
}

    

}
