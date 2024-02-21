<?php

namespace App\Controller;

use App\Entity\Trade;
use App\Form\TradeType;
use App\Repository\AssetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TradeController extends AbstractController
{
    #[Route('/trade', name: 'trade')]
    public function index(Request $request, EntityManagerInterface $entityManager, AssetRepository $assetRepository): Response
    {
        $trade = new Trade();
        $form = $this->createForm(TradeType::class, $trade);
        $form->handleRequest($request);

        $latestAsset = $assetRepository->findLatest(); // This fetches the latest asset data
        $assetCurrency = $latestAsset->getName();

        if ($form->isSubmitted() && $form->isValid()) {
            // Assume $this->getUser() returns the currently authenticated user
            $user = $this->getUser();
            $userCurrency = $user->getCurrency();

            if (!$user) {
                // Handle the case where there's no authenticated user
                throw new \Exception('No authenticated user found');
            }

            $trade->setUser($user); // Assuming the Trade entity has a setUser() method to associate the User entity

            $currentBidRate = $latestAsset->getBid(); // Assuming this method exists and gets the current bid

            // Set the entry rate for the trade to the latest bid rate
            $trade->setEntryRate($currentBidRate);

            $lotSize = 10; // Fixed lot size
            $tradeSize = $trade->getLotCount() * $lotSize;
            $trade->setTradeSize($tradeSize);

            // Set the initial status of the trade to "open"
            $trade->setStatus('open');

            // Assume $currentPrice is the latest price you fetched from your Asset entity
            $currentPrice = $latestAsset->getBid(); // or getAsk(), depending on your logic

            // Example PNL calculation
            $entryPrice = $trade->getEntryRate();
            $lotCount = $trade->getLotCount();
            $conversionRate = $this->getConversionRate($assetCurrency, $userCurrency);

            // Calculate pip value based on user's currency
            $pipValue = ($lotSize * $lotCount) * 0.01 * $conversionRate;

            // Adjust PNL calculation using pip value
            if ($trade->getPosition() === 'buy') {
                $pnl = ($currentPrice - $entryPrice) * $pipValue * 100;
            } else { // Assuming 'sell'
                $pnl = ($entryPrice - $currentPrice) * $pipValue * 100;
            }
            $trade->setPnl($pnl);

            $usedMargin = $tradeSize * 0.1 * $conversionRate * $currentPrice;
            $trade->setUsedMargin($usedMargin);

            // Set the agent in charge of the user making the trade
            $agentInCharge = $user->getAgentInCharge();
            if ($agentInCharge) {
                $trade->setAgent($agentInCharge);
            } else {
                // Handle the case where the current user has no assigned agent
                throw new \Exception('The current user has no assigned agent');
            }

            // Save trade
            $entityManager->persist($trade);
            $entityManager->flush();

            // Redirect or display a confirmation
            return $this->redirectToRoute('user_profile');
        }


        return $this->render('trade/index.html.twig', [
            'form' => $form->createView(),
            'latestAsset' => $latestAsset,
        ]);
    }

    private function getConversionRate(string $fromCurrency, string $toCurrency): float
    {
        // Simplified logic. You might fetch this from an API or define fixed rates.
        if ($fromCurrency === 'BTCUSDT' && $toCurrency === 'EUR') {
            return 0.9215; // Example rate
        }
        // Default to 1 if no conversion is needed or if the currency pair is not handled
        return 1;
    }
}
