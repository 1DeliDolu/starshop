<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Model\Starship;


class StarshipApiController extends AbstractController
{
    #[Route('/starships', name: 'starship_list')]
    public function starshipList()
    {
        $starships = [
            new Starship(1, 'Millennium Falcon', 'YT-1300', 'Corellian Engineering Corporation'),
            new Starship(2, 'X-wing', 'T-65 X-wing', 'Incom Corporation'),
            new Starship(3, 'Star Destroyer', 'Imperial I-class', 'Kuat Drive Yards'),
        ];

        // Convert Starship objects to arrays for JSON response
        $starships = array_map(function (Starship $starship) {
            return [
            'name' => $starship->getName(),
            'model' => $starship->getModel(),
            'manufacturer' => $starship->getManufacturer(),
            ];
        }, $starships);

        return $this->json($starships);
    }

}
