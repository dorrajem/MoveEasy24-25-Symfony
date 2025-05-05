<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Form\AvisType;
use App\Repository\AvisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/front/avis')]
class AvisController extends AbstractController
{
    #[Route('/', name: 'app_avis_index', methods: ['GET'])]
    public function index(AvisRepository $avisRepository): Response
    {
        return $this->render('avis/index.html.twig', [
            'avis' => $avisRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_avis_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $avis = new Avis();
        // Définir la date actuelle par défaut
        $avis->setDateAvis(new \DateTime());
        
        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($avis);
            $entityManager->flush();

            $this->addFlash('success', 'Votre avis a été ajouté avec succès !');
            
            return $this->redirectToRoute('app_avis_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('avis/new.html.twig', [
            'avis' => $avis,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_avis_show', methods: ['GET'])]
    public function show(Avis $avis): Response
    {
        return $this->render('avis/show.html.twig', [
            'avis' => $avis,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_avis_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Avis $avis, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Votre avis a été modifié avec succès !');
            
            return $this->redirectToRoute('app_avis_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('avis/edit.html.twig', [
            'avis' => $avis,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_avis_delete', methods: ['POST'])]
    public function delete(Request $request, Avis $avis, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$avis->getId(), $request->request->get('_token'))) {
            try {
                $entityManager->remove($avis);
                $entityManager->flush();
                $this->addFlash('success', 'L\'avis a été supprimé avec succès !');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la suppression de l\'avis.');
            }
        }

        return $this->redirectToRoute('app_avis_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/avis/{id}/reaction/{type}', name: 'app_avis_reaction', methods: ['POST'])]
public function handleReaction(Request $request, Avis $avis, string $type, EntityManagerInterface $entityManager): JsonResponse
{
    // Vérifiez si l'utilisateur est connecté
    $user = $this->getUser();
    if (!$user) {
        return $this->json(['success' => false, 'message' => 'Non authentifié'], 401);
    }

    // Récupérez ou créez la réaction
    $reaction = $avis->getReactions()->filter(
        fn($r) => $r->getUser() === $user
    )->first() ?: new Reaction();

    if ($type === 'like') {
        if ($reaction->getType() === 'like') {
            // Supprimer si déjà like
            $entityManager->remove($reaction);
            $action = 'removed';
        } else {
            $reaction->setType('like');
            $reaction->setUser($user);
            $reaction->setAvis($avis);
            $entityManager->persist($reaction);
            $action = 'added';
        }
    } elseif ($type === 'dislike') {
        if ($reaction->getType() === 'dislike') {
            // Supprimer si déjà dislike
            $entityManager->remove($reaction);
            $action = 'removed';
        } else {
            $reaction->setType('dislike');
            $reaction->setUser($user);
            $reaction->setAvis($avis);
            $entityManager->persist($reaction);
            $action = 'added';
        }
    }

    $entityManager->flush();

    // Comptez les likes/dislikes
    $likesCount = $avis->getReactions()->filter(
        fn($r) => $r->getType() === 'like'
    )->count();

    $dislikesCount = $avis->getReactions()->filter(
        fn($r) => $r->getType() === 'dislike'
    )->count();

    return $this->json([
        'success' => true,
        'action' => $action,
        'likes' => $likesCount,
        'dislikes' => $dislikesCount
    ]);
}

}