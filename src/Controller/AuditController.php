<?php

namespace App\Controller;

use App\Repository\AuditLogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/audit')]
class AuditController extends AbstractController
{
    #[Route('/', name: 'app_audit_index')]
    public function index(
        AuditLogRepository $auditLogRepository, 
        Request $request,
        PaginatorInterface $paginator
    ): Response {
        $actionFilter = $request->query->get('action');

        $queryBuilder = $auditLogRepository->createQueryBuilder('a')
            ->orderBy('a.loggedAt', 'DESC');

        if ($actionFilter) {
            $queryBuilder->where('a.action = :action')
                         ->setParameter('action', strtoupper($actionFilter));
        }

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10 // Logs per page
        );

        return $this->render('audit/index.html.twig', [
            'logs' => $pagination,
            'actionFilter' => $actionFilter,
        ]);
    }
}
