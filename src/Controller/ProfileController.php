<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\MakerBundle\FileManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index()
    {
        $user = $this->getUser();
        $avatar = $user->getAvatar() ?? "";

        return $this->render('Profile/index.html.twig', ['user' => $user, 'avatar' => $avatar]);
    }

    #[Route('/profile/update_avatar', name: 'app_update_avatar')]
    public function updateAvatar(Request $request, FileUploader $fileUploader, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();

        $avatar = $request->files->get('avatar');
        if ($avatar) {
            $dir = $this->getParameter('avatar_path');
            $dirname = "$dir". "/" . $user->getId();
            $avatar_name = $fileUploader->upload($avatar, $dirname);
            $user->setAvatar('uploads/users/avatars/'.$user->getId() . "/" . $avatar_name);

            $entityManager->persist($user);
            $entityManager->flush();
        }

        return $this->redirect('/profile');
    }
}