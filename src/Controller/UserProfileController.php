<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use App\Entity\User;

class UserProfileController extends AbstractController
{
    #[Route('/user-profile', name: 'user_profile')]
    public function index(Request $request, #[CurrentUser] $user = null): Response
    {
        if (!$user) {
            $user = $this->getUser();
        }

        $username = $user->getUsername();

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

        return $this->render('profile/user_profile.html.twig', [
            'username' => $username,
            'sessionStartTime' => $sessionStartTime,
            'remainingLifetime' => $remainingLifetime,
        ]);
    }
}
