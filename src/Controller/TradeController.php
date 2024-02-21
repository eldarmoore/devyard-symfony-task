<?php

namespace App\Controller;

use App\Entity\Asset;
use App\Entity\Trade;
use App\Entity\User;
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
        $latestAsset = $assetRepository->findLatest();

        $form = $this->createForm(TradeType::class, $trade);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$user = $this->getUser()) {
                throw new \Exception('No authenticated user found');
            }

            $this->handleTradeForm($trade, $latestAsset, $user->getCurrency(), $user);
            $entityManager->persist($trade);
            $entityManager->flush();

            return $this->redirectToRoute('user_profile');
        }

        return $this->render('trade/index.html.twig', [
            'form' => $form->createView(),
            'latestAsset' => $latestAsset,
        ]);
    }

    private function handleTradeForm(Trade $trade, Asset $latestAsset, string $userCurrency, User $user): void
    {
        $trade->setUser($user);
        $trade->setEntryRate($latestAsset->getBid());
        $trade->setTradeSize($this->calculateTradeSize($trade,$latestAsset));
        $trade->setStatus('open');
        $trade->setPnl($this->calculatePnl($trade, $latestAsset->getBid(), $userCurrency,$latestAsset));
        $trade->setUsedMargin($trade->getTradeSize() * 0.1 * $conversionRate * $latestAsset->getBid());

        if (!$agentInCharge = $user->getAgentInCharge()) {
            throw new \Exception('The current user has no assigned agent');
        }
        $trade->setAgent($agentInCharge);
    }

    private function calculateTradeSize(Trade $trade, Asset $latestAsset): float
    {
        return $trade->getLotCount() * $latestAsset->getLotSize();
    }

    private function calculatePnl(Trade $trade, string $currentPrice, string $userCurrency, Asset $latestAsset): float
    {
        $lotSize = $latestAsset->getLotSize();
        $entryPrice = $trade->getEntryRate();
        $lotCount = $trade->getLotCount();
        $conversionRate = $this->getConversionRate($latestAsset->getName(), $userCurrency);
        $pipValue = ($lotSize * $lotCount) * 0.01 * $conversionRate;

        return $trade->getPosition() === 'buy'
            ? ($currentPrice - $entryPrice) * $pipValue * 100
            : ($entryPrice - $currentPrice) * $pipValue * 100;
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
