<?php

namespace App\Controller;

use App\Repository\StarshipRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function homepage(StarshipRepository $starshipRepository): Response
    {
        $ships = $starshipRepository->findAll();
        if ($ships === []) {
            // Keine Schiffe vorhanden, leere Sicht rendern
            return $this->render('main/homepage.html.twig', [
                'myShip' => null,
                'ships' => [],
            ]);
        }

        // Zufälliges Schiff auswählen (nutzt random_int für bessere Lesbarkeit)
        $index = random_int(0, \count($ships) - 1);
        $myShip = $ships[$index];

        return $this->render('main/homepage.html.twig', [
            'myShip' => $myShip,
            'ships' => $ships,
        ]);
    }
}
