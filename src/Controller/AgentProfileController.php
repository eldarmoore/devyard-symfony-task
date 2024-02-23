<?php

namespace App\Controller;

use App\Entity\Trade;
use App\Form\AssignAgentsType;
use App\Form\AssignUsersType;
use App\Form\TradeType;
use App\Repository\AgentRepository;
use App\Repository\AssetRepository;
use App\Repository\UserRepository; // Ensure this is added if you need to query users
use App\Service\TradeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use App\Entity\Agent;

class AgentProfileController extends AbstractController
{
    #[Route('/agent-profile', name: 'agent_profile')]
    public function index(Request $request, AssetRepository $assetRepository, TradeService $tradeService, EntityManagerInterface $entityManager, #[CurrentUser] ?Agent $agent, UserRepository $userRepository, AgentRepository $agentRepository, SessionInterface $session): Response
    {
        if (!$agent) {
            return $this->redirectToRoute('login');
        }

        // Check if the session start time is not set, then set it
        $this->handleSessionStartTime($session);

        // Setup for AssignUsersType form
        $assignUsersFormResult = $this->handleAssignUsersForm($request, $entityManager);
        if ($assignUsersFormResult instanceof RedirectResponse) {
            return $assignUsersFormResult;
        }
        $assignUsersForm = $assignUsersFormResult;


        // Setup for AssignAgentsType form
        $assignAgentsFormResult = $this->handleAssignAgentsForm($request, $entityManager);
        if ($assignAgentsFormResult instanceof RedirectResponse) {
            return $assignAgentsFormResult;
        }
        $assignAgentsForm = $assignAgentsFormResult;

        // Trade Form Section
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

        $sessionDetails = $this->getSessionDetails($session);

        $viewData = [
            'username' => $username,
            'assignUsersForm' => $assignUsersForm->createView(),
            'assignAgentsForm' => $assignAgentsForm->createView(),
            'tradeForm' => $tradeForm->createView(),
            'latestAsset' => $latestAsset,
            'assignedUsers' => $usersWithAgent,
            'assignedAgents' => $agentsWithAgent,
            'unassignedUsers' => $usersWithoutAgent,
        ];

        $viewData = array_merge($sessionDetails, $viewData);

        return $this->render('profile/agent_profile.html.twig', $viewData);
    }

    private function handleAssignUsersForm(Request $request, EntityManagerInterface $entityManager): FormInterface
    {
        $assignUsersForm = $this->createForm(AssignUsersType::class);
        $assignUsersForm->handleRequest($request);

        if ($assignUsersForm->isSubmitted() && $assignUsersForm->isValid()) {
            $selectedUsers = $assignUsersForm->get('users')->getData();
            $currentAgent = $this->getUser();
            foreach ($selectedUsers as $user) {
                // Assign the current agent to each selected user
                $user->setAgentInCharge($currentAgent);
                $entityManager->persist($user);
            }
            $entityManager->flush();
            // Add a flash message or any other form of success notification
            $this->addFlash('success', 'Users successfully assigned.');
        }
        return $assignUsersForm;
    }

    private function handleAssignAgentsForm(Request $request, EntityManagerInterface $entityManager): FormInterface
    {
        $assignAgentsForm = $this->createForm(AssignAgentsType::class);
        $assignAgentsForm->handleRequest($request);
        if ($assignAgentsForm->isSubmitted() && $assignAgentsForm->isValid()) {
            $selectedAgents = $assignAgentsForm->get('agents')->getData();
            $currentAgent = $this->getUser();
            foreach ($selectedAgents as $agent) {
                // Detach the agent from the entity manager's persistence context
                $entityManager->detach($agent);

                // Assign the current agent as the agentInCharge for each selected agent
                $agent->setAgentInCharge($currentAgent);

                // Persist the updated agent
                $entityManager->persist($agent);
            }
            $entityManager->flush();
            // Add a flash message or any other form of success notification
            $this->addFlash('success', 'Agents successfully assigned.');
        }
        return $assignAgentsForm;
    }

    private function handleTradeForm(Request $request, AssetRepository $assetRepository, TradeService $tradeService, EntityManagerInterface $entityManager, Agent $agent)
    {
        $trade = new Trade();
        $tradeForm = $this->createForm(TradeType::class, $trade, [
            'is_agent' => true,
            'agent' => $agent, // Make sure $agent is an Agent entity
        ]);
        $tradeForm->handleRequest($request);

        // Check if the form is submitted and valid
        if ($tradeForm->isSubmitted() && $tradeForm->isValid()) {
            $task = $tradeForm->getData();
            dd($task);
            $selectedUser = $tradeForm->get('user')->getData(); // Make sure you're fetching from $tradeForm

            // Validate if a user is selected
            if (!$selectedUser) {
                // Handle the case where no user is selected
                $this->addFlash('error', 'Please select a user for the trade.');
                return $tradeForm;
            }

            // Set the agent ID in the trade entity
            $trade->setAgent($agent); // Assuming the Trade entity has a method like setAgent

            $trade->setUser($selectedUser); // Ensure the Trade entity is updated with the selected user
            $userCurrency = $selectedUser->getCurrency();
            $tradeService->handleTrade($trade, $assetRepository->findLatest(), $userCurrency, $selectedUser);
            return $this->redirectToRoute('agent_trades');
        }
        return $tradeForm;
    }
    private function handleSessionStartTime(SessionInterface $session): void {
        if (!$session->has('session_start_time')) {
            $session->set('session_start_time', time());
        }
    }

    private function getSessionDetails(SessionInterface $session): array {
        $sessionStartTime = $session->get('session_start_time');
        $timeElapsed = time() - $sessionStartTime;
        $sessionMaxTime = $this->getParameter('session_max_time');
        $remainingLifetime = max($sessionMaxTime - $timeElapsed, 0);

        return [
            'sessionStartTime' => $sessionStartTime,
            'sessionMaxTime' => $sessionMaxTime,
            'remainingLifetime' => $remainingLifetime,
        ];
    }
}
