<?php

namespace App\Controller;

use App\Entity\Trade;
use App\Form\AssignUsersType;
use App\Form\TradeType;
use App\Repository\AgentRepository;
use App\Repository\AssetRepository;
use App\Repository\UserRepository; // Ensure this is added if you need to query users
use App\Service\LoggingService;
use App\Service\TradeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use App\Entity\Agent;

class AgentProfileController extends AbstractController
{
    private LoggingService $loggingService;

    public function __construct(LoggingService $loggingService)
    {
        $this->loggingService = $loggingService;
    }

    #[Route('/agent-profile', name: 'agent_profile')]
    public function index(Request $request, AssetRepository $assetRepository, TradeService $tradeService, EntityManagerInterface $entityManager, #[CurrentUser] ?Agent $agent, UserRepository $userRepository, AgentRepository $agentRepository, SessionInterface $session): Response
    {
        if (!$agent) {
            return $this->redirectToRoute('login');
        }

        // Check if the session start time is not set, then set it
        if (!$session->has('session_start_time')) {
            $session->set('session_start_time', time());
        }

        // Setup for AssignUsersType form
        $assignUsersForm = $this->createForm(AssignUsersType::class);
        $assignUsersForm->handleRequest($request);

        if ($assignUsersForm->isSubmitted() && $assignUsersForm->isValid()) {
            $selectedUsers = $assignUsersForm->get('users')->getData(); // Assuming it returns a collection of User entities

            // Get the current agent
            $currentAgent = $this->getUser();

            foreach ($selectedUsers as $user) {
                // Assign the current agent to each selected user
                $user->setAgentInCharge($currentAgent);
                $entityManager->persist($user);
            }

            $entityManager->flush();

            // Add a flash message or any other form of success notification
            $this->addFlash('success', 'Users successfully assigned.');

            // Add to Log
            $this->loggingService->logUserAssignment($currentAgent);

            return $this->redirectToRoute('agent_profile');
        }

        // Setup for TradeType form
        $trade = new Trade();
        $tradeForm = $this->createForm(TradeType::class, $trade, [
            'is_agent' => true,
            'agent' => $agent, // Make sure $agent is an Agent entity
        ]);
        $tradeForm->handleRequest($request);
        if ($tradeForm->isSubmitted() && $tradeForm->isValid()) {
            $selectedUser = $tradeForm->get('user')->getData(); // Make sure you're fetching from $tradeForm
            if (!$selectedUser) {
                throw new \Exception('No user selected for the trade');
            }

            // Set the agent ID in the trade entity
            $trade->setAgent($agent); // Assuming the Trade entity has a method like setAgent

            $trade->setUser($selectedUser); // Ensure the Trade entity is updated with the selected user
            $userCurrency = $selectedUser->getCurrency();
            $tradeService->handleTrade($trade, $assetRepository->findLatest(), $userCurrency, $selectedUser);
            return $this->redirectToRoute('agent_trades');
        }

        // Retrieve users without an agent assigned to them
        $usersWithoutAgent = $userRepository->findBy(['agentInCharge' => null]);

        // Retrieve users assigned to the agent
        $usersWithAgent = $userRepository->findBy(['agentInCharge' => $agent]);

        $agentsWithAgent = $agentRepository->findBy(['agentInCharge' => $agent]);

        $latestAsset = $assetRepository->findLatest();

        // Define the missing variables
        $username = $agent->getUsername(); // Assuming Agent entity has getUsername method

        // Session time details for displaying session duration
        $sessionStartTime = $request->getSession()->get('session_start_time');
        $timeElapsed = time() - $sessionStartTime;
        $sessionMaxTime = $this->getParameter('session_max_time');
        $remainingLifetime = max($sessionMaxTime - $timeElapsed, 0);

        return $this->render('profile/agent_profile.html.twig', [
            'username' => $username,
            'sessionStartTime' => $sessionStartTime,
            'sessionMaxTime' => $sessionMaxTime,
            'remainingLifetime' => $remainingLifetime,
            'assignUsersForm' => $assignUsersForm->createView(),
            'tradeForm' => $tradeForm->createView(),
            'latestAsset' => $latestAsset,
            'assignedUsers' => $usersWithAgent,
            'assignedAgents' => $agentsWithAgent,
            'unassignedUsers' => $usersWithoutAgent,
        ]);
    }
}
