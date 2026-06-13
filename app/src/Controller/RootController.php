<?php

namespace App\Controller;

use App\Service\Fiscal\FiscalService;
use App\Service\TableService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class RootController extends AbstractController
{
    #[Route('/root', name: 'app_root')]
    public function index(
        FiscalService $service,
        TableService $tableService,
    ): JsonResponse
    {
        $imagePath = '../tmp/qr2.jpg';

        $check = $service->loadCheckData($imagePath);
        $tableService
            ->setTotal($check)
            ->setPersonCount(2)
            ->setCheckItems($check->getItems())
            ->generate();

        return $this->json([
            'qr_data' => "ok",
        ]);
    }
}
