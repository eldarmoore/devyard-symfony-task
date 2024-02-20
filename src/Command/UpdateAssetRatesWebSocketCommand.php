<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WebSocket\Client;

class UpdateAssetRatesWebSocketCommand extends Command
{
    protected static $defaultName = 'app:update-asset-rates-websocket';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $client = new Client("wss://stream.binance.com:9443/ws/btcusdt@bookTicker");

        while (true) {
            try {
                $message = $client->receive();

                echo $message;
                // Act on received message
                // Break while loop to stop listening
            } catch (\WebSocket\ConnectionException $e) {
                // Possibly log errors
            }
        }
        $client->close();

        return Command::SUCCESS;
    }
}
