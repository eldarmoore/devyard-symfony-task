<?php

namespace App\Service;

use App\Entity\Asset;
use App\Entity\Trade;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class TradeService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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
    }

    private function calculateTradeSize(Trade $trade): float
    {
        // Assuming lot size is a constant value or retrieved from the trade/asset
        $lotSize = 10; // This can be dynamic if needed
        return $trade->getLotCount() * $lotSize;
    }

    private function calculatePnl(Trade $trade, Asset $latestAsset, string $userCurrency): float
    {
        // Simplified PNL calculation
        // This method should include currency conversion if the user currency differs from the asset currency
        $currentPrice = $latestAsset->getBid(); // or getAsk(), depending on trade position
        $entryPrice = $trade->getEntryRate();
        $tradeSize = $trade->getTradeSize();
        $conversionRate = $this->getConversionRate($userCurrency); // Implement this method based on your conversion logic

        if ($trade->getPosition() === 'buy') {
            return ($currentPrice - $entryPrice) * $tradeSize * $conversionRate;
        } else {
            return ($entryPrice - $currentPrice) * $tradeSize * $conversionRate;
        }
    }

    private function calculateUsedMargin(float $tradeSize, Asset $latestAsset, string $userCurrency): float
    {
        // Example calculation, adjust as necessary
        $marginRate = 0.1; // Example margin rate
        $conversionRate = $this->getConversionRate($userCurrency); // Implement this method
        return $tradeSize * $marginRate * $conversionRate;
    }

    private function getConversionRate(string $userCurrency): float
    {
        // Implement currency conversion logic here
        // Return 1 if no conversion is needed or if user currency matches asset currency
        return 1.0;
    }
}
