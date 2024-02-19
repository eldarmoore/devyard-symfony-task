<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use App\Entity\User;
use App\Entity\Agent;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile')]
    public function index(Request $request, #[CurrentUser] $user = null): Response
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

        // Assuming session start time is stored at login or session start
        $sessionStartTime = $request->getSession()->get('session_start_time');
        if (!$sessionStartTime) {
            $sessionStartTime = time();
            $request->getSession()->set('session_start_time', $sessionStartTime);
        }

        $currentTime = time();
        $timeElapsed = $currentTime - $sessionStartTime;
        $sessionMaxTime = $this->getParameter('session_max_time'); // Access the parameter
        $remainingLifetime = max($sessionMaxTime - $timeElapsed, 0);

        return $this->render('profile/index.html.twig', [
            'username' => $username,
            'sessionStartTime' => $sessionStartTime,
            'remainingLifetime' => $remainingLifetime,
        ]);
    }
}
