<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Model\Starship;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\StarshipRepository;

class StarshipApiController extends AbstractController
{
    #[Route('/api/starships', name: 'starship_list', methods: ['GET'])]
    public function getCollection(StarshipRepository $starshipRepository): Response
    {
        // Fetch starships from the repository
        $starships = $starshipRepository->findAll();



        return $this->json($starships);
    }

    #[Route('/api/starships/{id<\d+>}', name:'starship_show')]
    public function getItem(StarshipRepository $starshipRepository, int $id): Response
    {
        $starship = $starshipRepository->findOneById($id);

        if (!$starship) {
            throw $this->createNotFoundException('Starship not found');
        }

        return $this->json($starship);
    }

}
