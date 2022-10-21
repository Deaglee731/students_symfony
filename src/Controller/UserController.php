<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\ScoreRepository;
use App\Repository\UserRepository;
use App\Services\FileUploader;
use App\Services\PdfService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(Request $request, Environment $twig, UserRepository $userRepository): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));

        $filteredquery = $userRepository->findByField($request);

        $paginator = $userRepository->getUserPaginator($offset, $filteredquery);

        return $this->render('user/index.html.twig', [
            'users' => $paginator,
            'previous' => $offset - UserRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + UserRepository::PAGINATOR_PER_PAGE),
            'request' => $request,
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request,EntityManagerInterface $entityManager, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher, ManagerRegistry $doctrine, FileUploader $fileUploader): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user, true);

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $user->setRoles(['ROlE_STUDENT']);
            $entityManager->persist($user);
            $entityManager->flush();

            $avatar = $form->get('avatar')->getData();
            if ($avatar) {
                $dir = $this->getParameter('avatar_path');
                $dirname = "$dir". "/" . $user->getId();
                $avatar_name = $fileUploader->upload($avatar, $dirname);
                $user->setAvatar('uploads/users/avatars/'.$user->getId() . "/" . $avatar_name);

                $entityManager->persist($user);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user, true);


            $avatar = $form->get('avatar')->getData();
            if ($avatar) {
                $dir = $this->getParameter('avatar_path');
                $dirname = "$dir". "/" . $user->getId();
                $avatar_name = $fileUploader->upload($avatar, $dirname);
                $user->setAvatar($avatar_name);
            }

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository, ScoreRepository $scoreRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            foreach ($scoreRepository->findByUser($user) as $score) {
                $scoreRepository->remove($score);
            }

            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/students/export', name: 'app_user_export', methods: ['GET'])]
    public function export(UserRepository $userRepository, PdfService $pdfService)
    {
        $users = $userRepository->findAll();

        $html = $this->renderView('user/list.html.twig', [
            'users' => $users
        ]);

        $pdfService->generatePDF($html, 'StudentList');
    }

    #[Route('/students/{id}/restore', name: 'app_user_restore', methods: ['GET'])]
    public function restore(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $entityManager->getFilters()->disable('softdeleteable');
        $user = $userRepository->findOneBy(['id' => $request->get('id')]);
        $user->setDeletedAt(null);
        $entityManager->flush();

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/students/{id}/forceDelete', name: 'app_user_force_delete', methods: ['GET'])]
    public function forceDelete(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, ScoreRepository $scoreRepository)
    {
        $entityManager->getFilters()->disable('softdeleteable');
        $user = $userRepository->findOneBy(['id' => $request->get('id')]);

        if ($user->isDeleted()) {
            foreach ($scoreRepository->findByUser($user) as $score) {
                $scoreRepository->remove($score);
            }
        }
        $entityManager->remove($user);
        $entityManager->flush();

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
