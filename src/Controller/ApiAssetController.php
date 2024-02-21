<?php

namespace App\Controller;

use App\Repository\AssetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiAssetController extends AbstractController
{
    #[Route('/api/latest-bid', name: 'api_latest_bid', methods: ['GET'])]
    public function latestBid(AssetRepository $assetRepository): JsonResponse
    {
        $latestAsset = $assetRepository->findLatest();
        return $this->json([
            'bid' => $latestAsset->getBid(),
            'dateUpdate' => $latestAsset->getDateUpdate()->format('Y-m-d H:i:s'),
        ]);
    }
}
