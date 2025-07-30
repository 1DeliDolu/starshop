<?php

namespace App\Controller;

use App\Repository\StarshipRepository;
use App\Twig\Runtime\AppExtensionRuntime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Starship;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function homepage(
        StarshipRepository $starshipRepository,
        AppExtensionRuntime $appExtensionRuntime,
        EntityManagerInterface $em,
    ): Response {
        $ships = $em->createQueryBuilder()
            ->select('s')
            ->from(Starship::class, 's')
            ->getQuery()
            ->getResult();
        $myShip = $ships[array_rand($ships)];

        // ISS verisini al
        $issData = $appExtensionRuntime->getIssLocationData();

        return $this->render('main/homepage.html.twig', [
            'myShip' => $myShip,
            'ships' => $ships,
            'issData' => $issData,
        ]);
    }
}
