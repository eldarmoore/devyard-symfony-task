<?php

namespace App\Service;

use App\Entity\Asset;
use App\Entity\Trade;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class TradeService
{
    private EntityManagerInterface $entityManager;
    private LoggingService $loggingService;

    public function __construct(EntityManagerInterface $entityManager, LoggingService $loggingService)
    {
        $this->entityManager = $entityManager;
        $this->loggingService = $loggingService;
    }

    public function handleTrade(Trade $trade, Asset $latestAsset, string $userCurrency, User $user): void
    {
        // Set the user and the latest bid as the entry rate for the trade
        $trade->setUser($user);
        $trade->setEntryRate($latestAsset->getBid());

        // Calculate trade size
        $tradeSize = $this->calculateTradeSize($trade);
        $trade->setTradeSize($tradeSize);

        // Calculate PNL based on the current asset price and the trade details
        $pnl = $this->calculatePnl($trade, $latestAsset, $userCurrency);
        $trade->setPnl($pnl);

        // Calculate used margin
        $usedMargin = $this->calculateUsedMargin($tradeSize, $latestAsset, $userCurrency);
        $trade->setUsedMargin($usedMargin);

        // Set initial status of the trade
        $trade->setStatus('open');

        // Persist and flush the trade entity
        $this->entityManager->persist($trade);
        $this->entityManager->flush();

        // Log the trade creation
        $this->loggingService->logTradeCreation($user);
    }

    private function calculateTradeSize(Trade $trade): float
    {
        $lotSize = 10;
        return $trade->getLotCount() * $lotSize;
    }

    private function calculatePnl(Trade $trade, Asset $latestAsset, string $userCurrency): float
    {
        $currentPrice = $latestAsset->getBid();
        $entryPrice = $trade->getEntryRate();
        $tradeSize = $trade->getTradeSize();
        $conversionRate = $this->getConversionRate($userCurrency);

        if ($trade->getPosition() === 'buy') {
            return ($currentPrice - $entryPrice) * $tradeSize * $conversionRate;
        } else {
            return ($entryPrice - $currentPrice) * $tradeSize * $conversionRate;
        }
    }

    private function calculateUsedMargin(float $tradeSize, Asset $latestAsset, string $userCurrency): float
    {
        $marginRate = 0.1;
        $conversionRate = $this->getConversionRate($userCurrency);

        // Get the bid currency price and convert it to the user's currency
        $bidCurrencyPrice = $latestAsset->getBid(); // Assuming this returns the bid price in the bid currency
        $bidCurrencyToUserCurrency = $bidCurrencyPrice * $conversionRate;

        return $tradeSize * $marginRate * $bidCurrencyToUserCurrency;
    }

    private function getConversionRate(string $userCurrency): float
    {
        if ($userCurrency === 'USD') {
            return 1.0; // No conversion needed
        } elseif ($userCurrency === 'EUR') {
            return 0.9215; // USD to EUR conversion rate
        } else {
            // Handle other currencies if needed
            return 1.0; // Default to no conversion
        }
    }
}
