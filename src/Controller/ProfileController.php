<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index()
    {
        $user = $this->getUser();

        return $this->render('Profile/index.html.twig', ['user' => $user]);
    }
}