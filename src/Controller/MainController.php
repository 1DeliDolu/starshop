<?php

namespace App\Controller;

use App\Repository\StarshipRepository;
use Symfony\Bridge\Twig\Command\DebugCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function homepage(
        StarshipRepository $starshipRepository,
        HttpClientInterface $client,
        CacheInterface $issLocationPool,
        #[Autowire(param: 'iss_location_cache_ttl')]
        int $issLocationCacheTtl,
        #[Autowire(service: 'twig.command.debug')]
        DebugCommand $twigDebugCommand,
    ): Response {
        $ships = $starshipRepository->findAll();
        $myShip = $ships[array_rand($ships)];
        $response = $client->request('GET', 'https://api.wheretheiss.at/v1/satellites/25544');
        $issData = $issLocationPool->get('iss_location_data', function (ItemInterface $item) use ($client): array {
            //$item->expiresAfter(5); // Cache for 5 seconds
            $response = $client->request('GET', 'https://api.wheretheiss.at/v1/satellites/25544');
            return $response->toArray();
        });

        return $this->render('main/homepage.html.twig', [
            'myShip' => $myShip,
            'ships' => $ships,
            'issData' => $issData,
        ]);
    }
}
