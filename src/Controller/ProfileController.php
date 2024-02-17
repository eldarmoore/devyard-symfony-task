<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use App\Entity\User;
use App\Entity\Agent;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile')]
    public function index(#[CurrentUser] $user = null): Response
    {
        if (!$user) {
            $user = $this->getUser();
        }

        if ($user instanceof User) {
            $username = $user->getUsername();
        } elseif ($user instanceof Agent) {
            $username = $user->getUsername();
            $role = $user->getRole();
        } else {
            throw $this->createAccessDeniedException('No authenticated user or agent.');
        }

        return $this->render('profile/index.html.twig', [
            'username' => $username
        ]);
    }
}
