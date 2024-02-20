<?php

namespace App\Command;

use App\Entity\Asset;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WebSocket\Client;

class UpdateAssetRatesWebSocketCommand extends Command
{
    protected static $defaultName = 'app:update-asset-rates-websocket';
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $client = new Client("wss://stream.binance.com:9443/ws/btcusdt@bookTicker");
        $lastUpdateTimestamp = time();

        while (true) {
            $currentTimestamp = time();

            // Only proceed if at least 1 second has passed since the last update
            if ($currentTimestamp - $lastUpdateTimestamp >= 1) {
                try {
                    $message = $client->receive();
                    $data = json_decode($message, true);
                    $bid = $data['b']; // Assuming 'b' is the bid key
                    $ask = $data['a']; // Assuming 'a' is the ask key

                    $asset = new Asset();
                    $asset->setName('BTC/USD');
                    $asset->setBid($bid);
                    $asset->setAsk($ask);
                    $asset->setDateUpdate(new \DateTime());

                    $this->entityManager->persist($asset);
                    $this->entityManager->flush();
                    $this->entityManager->clear(); // Detach all objects from Doctrine to free memory

                    $lastUpdateTimestamp = $currentTimestamp; // Update the timestamp of the last update

                } catch (\WebSocket\ConnectionException $e) {
                    $output->writeln("Error: " . $e->getMessage());
                    // Possibly log errors or attempt to reconnect
                }
            }

            // Optional: sleep for a very short time to avoid high CPU usage
            usleep(100000); // Sleep for 0.1 second
        }

        $client->close();
        return Command::SUCCESS;
    }
}
