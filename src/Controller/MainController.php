<?php
namespace App\Controller;

use App\Repository\StarshipRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route(path: "/")]
    public function homepage(StarshipRepository $starshipRepository): Response
    {
       $ships=$starshipRepository->findAll();
       $starshipCount = count($ships);
       // This is the homepage action
       return $this->render('layout/homepage.html.twig', [
           'ships' => $ships,
       ]);
    }
}
