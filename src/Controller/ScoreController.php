<?php

namespace App\Controller;

use App\Entity\Score;
use App\Entity\Subject;
use App\Entity\User;
use App\Form\ScoreType;
use App\Repository\ScoreRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[Route('/user/{user}/score')]
class ScoreController extends AbstractController
{
    public $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    #[Route('/', name: 'app_score_index', methods: ['GET'])]
    public function index(User $user, ScoreRepository $scoreRepository): Response
    {
        return $this->render('score/index.html.twig', [
            'user' => $user,
            'scores' => $scoreRepository->findByUser($user),
        ]);
    }

    #[Route('/new', name: 'app_score_new', methods: ['GET', 'POST'])]
    public function new(User $user, Request $request, ScoreRepository $scoreRepository, ManagerRegistry $doctrine): Response
    {
        $score = new Score();
        $score->setUser($user);

        $subjects = $doctrine->getRepository(Subject::class)->findSubjectsWithoutScoreByUser($user);

        $form = $this->createForm(ScoreType::class, $score, [
            'subject' => $subjects,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $scoreRepository->add($score, true);

            return $this->redirectToRoute('app_score_index', ['user' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('score/new.html.twig', [
            'score' => $score,
            'subject' => $subjects,
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_score_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request,User $user, Score $score, ScoreRepository $scoreRepository, ManagerRegistry $doctrine): Response
    {
        $score->setUser($user);

        $form = $this->createForm(ScoreType::class, $score);
        $score->setSubject($score->getSubject());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $scoreRepository->add($score, true);

            return $this->redirectToRoute('app_score_index', ['user' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('score/edit.html.twig', [
            'score' => $score,
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_score_delete', methods: ['POST'])]
    public function delete(Request $request,User $user, Score $score, ScoreRepository $scoreRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$score->getId(), $request->request->get('_token'))) {
            $scoreRepository->remove($score, true);
        }

        return $this->redirectToRoute('app_score_index', ['user' => $user->getId()], Response::HTTP_SEE_OTHER);
    }
}
