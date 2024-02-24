<?php

namespace App\Controller;

use App\Entity\Trade;
use App\Form\TradeType;
use App\Repository\AssetRepository;
use App\Service\TradeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use App\Entity\User;

class UserProfileController extends AbstractController
{
    #[Route('/user-profile', name: 'user_profile')]
    public function index(Request $request, AssetRepository $assetRepository, TradeService $tradeService, #[CurrentUser] ?User $user): Response
    {
        if (!$user) {
            return $this->redirectToRoute('login');
        }

        $username = $user->getUsername();
        $latestAsset = $assetRepository->findLatest();

        // Create and handle the form for a new trade
        $trade = new Trade();
        $form = $this->createForm(TradeType::class, $trade, [
            'user_currency' => $user->getCurrency(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $agentInCharge = $user->getAgentInCharge();
            if ($agentInCharge) {
                $trade->setAgent($agentInCharge);
            }

            $tradeService->handleTrade($trade, $latestAsset, $user->getCurrency(), $user);
            return $this->redirectToRoute('user_profile');
        }

        if (!$request->getSession()->has('session_start_time')) {
            // Set the session start time only if it's not already set
            $request->getSession()->set('session_start_time', time());
        }

        // Session time details for displaying session duration
        $sessionStartTime = $request->getSession()->get('session_start_time');
        $timeElapsed = time() - $sessionStartTime;
        $sessionMaxTime = $this->getParameter('session_max_time');
        $remainingLifetime = max($sessionMaxTime - $timeElapsed, 0);

        return $this->render('profile/user_profile.html.twig', [
            'username' => $username,
            'form' => $form->createView(),
            'latestAsset' => $latestAsset,
            'sessionStartTime' => $sessionStartTime,
            'sessionMaxTime' => $sessionMaxTime,
            'remainingLifetime' => $remainingLifetime,
        ]);
    }
}
