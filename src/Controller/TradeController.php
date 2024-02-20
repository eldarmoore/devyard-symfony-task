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

        if ($form->isSubmitted() && $form->isValid()) {
            // Assume $this->getUser() returns the currently authenticated user
            $user = $this->getUser();

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
            if ($trade->getPosition() === 'buy') {
                $pnl = ($currentPrice - $entryPrice) * $tradeSize;
            } else { // Assuming 'sell'
                $pnl = ($entryPrice - $currentPrice) * $tradeSize;
            }
            $trade->setPnl($pnl);

            // Assuming you have the conversion rate available
            $conversionRate = 1; // This should be dynamic based on user's currency
            $usedMargin = $tradeSize * 0.1 * $conversionRate * $currentPrice;
            $trade->setUsedMargin($usedMargin);

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
}
