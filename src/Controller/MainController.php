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
        // This is the homepage action
        return new Response("<strong>Starshop: </strong> Welcome to the Starshop homepage!");
    }
}
