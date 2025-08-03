<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class WebhookTestController extends AbstractController
{
    #[Route('/webhook/test', name: 'webhook_test', methods: ['GET', 'POST'])]
    public function test(Request $request): JsonResponse
    {
        return new JsonResponse([
            'method' => $request->getMethod(),
            'headers' => $request->headers->all(),
            'content' => $request->getContent(),
            'query' => $request->query->all(),
            'message' => 'Webhook test endpoint is working!'
        ]);
    }
}
