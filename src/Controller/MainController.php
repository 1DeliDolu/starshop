<?php

namespace App\Controller;

use App\Repository\StarshipRepository;
use App\Twig\Runtime\AppExtensionRuntime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Starship;
use Symfony\Component\HttpFoundation\Request;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function homepage(
        StarshipRepository $repository,
        AppExtensionRuntime $appExtensionRuntime,
        EntityManagerInterface $em,
        Request $request,
    ): Response {

        $ships = $repository->findIncomplete();
        $ships->setMaxPerPage(5);
        $ships->setCurrentPage($request->query->get('page', 1));
        $myShip = $repository->findMyShip();

        // ISS verisini al
        $issData = $appExtensionRuntime->getIssLocationData();

        return $this->render('main/homepage.html.twig', [
            'myShip' => $myShip,
            'ships' => $ships,
            'issData' => $issData,
        ]);
    }
}
