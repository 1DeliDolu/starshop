<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route(path: "/")]
    public function homepage()
    {
        //myShip is a Starship array name, class, captain, status
        $myShip = [
            'name' => 'Millennium Falcon',
            'class' => 'Light Freighter',
            'captain' => 'Han Solo',
            'status' => 'Active'
        ];
        $starShipCount = 245;
        // This is the homepage action
        return $this->render('layout/homepage.html.twig', [
            'starShipCount' => $starShipCount,
            'myShip' => $myShip
        ]);
    }
}
