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

        $latestAsset = $assetRepository->findLatest(); // Implement findLatest() method in your repository to get the latest BTC/USD rate

        if ($form->isSubmitted() && $form->isValid()) {
            $lotSize = 10; // Fixed lot size
            $tradeSize = $trade->getLotCount() * $lotSize;

            // Example calculation for PNL, adjust according to your needs
            // $pnl = calculation based on position, entry rate, current rate, and lot count

            // Save trade
            $entityManager->persist($trade);
            $entityManager->flush();

            // Redirect or display a confirmation
            return $this->redirectToRoute('trade_confirmation'); // Implement this route as needed
        }

        return $this->render('trade/index.html.twig', [
            'form' => $form->createView(),
            'latestAsset' => $latestAsset,
        ]);
    }
}
