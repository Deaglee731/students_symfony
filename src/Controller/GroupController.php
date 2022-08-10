<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Subject;
use App\Entity\User;
use App\Form\GroupType;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use App\Services\JournalService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[Route('/group')]
class GroupController extends AbstractController
{
    public $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    #[Route('/', name: 'app_group_index', methods: ['GET'])]
    public function index(GroupRepository $groupRepository): Response
    {
        return $this->render('group/index.html.twig', [
            'groups' => $groupRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_group_new', methods: ['GET', 'POST'])]
    public function new(Request $request, GroupRepository $groupRepository, ManagerRegistry $doctrine): Response
    {
        $group = new Group();
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $groupRepository->add($group, true);

            return $this->redirectToRoute('app_group_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('group/new.html.twig', [
            'group' => $group,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_group_show', methods: ['GET'])]
    public function show(Group $group, ManagerRegistry $doctrine): Response
    {
        $users = $group->getUsers();

        return $this->render('group/show.html.twig', [
            'group' => $group,
            'users' => $users,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_group_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Group $group, GroupRepository $groupRepository): Response
    {
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $groupRepository->add($group, true);

            return $this->redirectToRoute('app_group_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('group/edit.html.twig', [
            'group' => $group,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_group_delete', methods: ['POST'])]
    public function delete(Request $request, Group $group, GroupRepository $groupRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$group->getId(), $request->request->get('_token'))) {
            $groupRepository->remove($group, true);
        }

        return $this->redirectToRoute('app_group_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/showJournal', name: 'app_group_showjournal', methods: ['GET'])]
    public function showJournal(Group $group, GroupRepository $groupRepository, JournalService $journalService)
    {
        $allStudentScoresList = $journalService->getJournalALLStudents($group);
        $subjects = $this->doctrine->getRepository(Subject::class)->findAll();

        return $this->render('group/journal.html.twig',[
            'group' => $group,
            'allStudentScoresList' => $allStudentScoresList,
            'subjects' => $subjects,
        ]);
    }
}
