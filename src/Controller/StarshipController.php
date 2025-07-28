<?php

namespace App\Controller;

use App\Repository\StarshipRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class StarshipController extends AbstractController
{
    #[Route(path: "/starships/{id<\d+>}", name: "starship_show")]
    public function show(int $id, StarshipRepository $starshipRepository): Response
    {
        // Your logic to retrieve starships goes here
        $ship = $starshipRepository->findOneById($id);

        if (!$ship) {
            throw $this->createNotFoundException('Starship not found');
        }

        return $this->render('starship/show.html.twig', [
            'ship' => $ship,
        ]);
    }
}

