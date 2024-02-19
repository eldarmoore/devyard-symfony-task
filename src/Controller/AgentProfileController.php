<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use App\Entity\Agent;

class AgentProfileController extends AbstractController
{
    #[Route('/agent-profile', name: 'agent_profile')]
    public function index(Request $request, #[CurrentUser] ?Agent $agent): Response
    {
        if (!$agent) {
            // Redirect to login if there's no authenticated agent
            return $this->redirectToRoute('login');
        }

        $username = $agent->getUsername();


        // Calculate session time details
        $sessionStartTime = $request->getSession()->get('session_start_time', time());
        $timeElapsed = time() - $sessionStartTime;
        $sessionMaxTime = $this->getParameter('session_max_time');
        $remainingLifetime = max($sessionMaxTime - $timeElapsed, 0);

        // Render the agent profile template with the full agent entity and session details
        return $this->render('profile/agent_profile.html.twig', [
            'username' => $username,
            'sessionStartTime' => $sessionStartTime,
            'remainingLifetime' => $remainingLifetime,
        ]);
    }
}
