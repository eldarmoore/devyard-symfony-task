<?php

namespace App\Command;

use App\Entity\Asset;
use App\Service\BinanceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateAssetRatesCommand extends Command
{
    protected static $defaultName = 'app:update-asset-rates';
    private $binanceService;
    private $entityManager;

    public function __construct(BinanceService $binanceService, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->binanceService = $binanceService;
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this->setDescription('Updates the BTC/USD rates from Binance.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $rates = $this->binanceService->fetchLatestRates();
        // Assuming you're updating a single BTC/USD asset record here. Adjust the logic as needed.
        $asset = $this->entityManager->getRepository(Asset::class)->findOneBy(['name' => 'BTC/USD']) ?? new Asset();
        $asset->setName('BTC/USD');
        $asset->setBid($rates['bid']);
        $asset->setAsk($rates['ask']);
        $asset->setDateUpdate($rates['timestamp']);

        $this->entityManager->persist($asset);
        $this->entityManager->flush();

        $output->writeln('Asset rates updated successfully.');

        return Command::SUCCESS;
    }
}
