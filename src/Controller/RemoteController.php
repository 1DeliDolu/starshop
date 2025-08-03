<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use function Symfony\Component\String\u;

final class RemoteController extends AbstractController
{
    #[Route('/', name: 'home', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        if ('POST' === $request->getMethod()) {
            switch ($button = $request->request->getString('button')) {
                case 'power':
                    dump('Power on/off the TV');
                    break;
                case 'channel-up':
                    dump('Change the channel up');
                    break;
                case 'channel-down':
                    dump('Change the channel down');
                    break;
                case 'volume-up':
                    dump('Increase the volume');
                    break;
                case 'volume-down':
                    dump('Decrease the volume');
                    break;
                default:
                    throw $this->createNotFoundException(sprintf('Button "%s" not found.', $button));
            }

            $this->addFlash('success', sprintf('%s pressed', u($button)->replace('-', ' ')->title(allWords: true)));

            return $this->redirectToRoute('home');
        }

        return $this->render('index.html.twig');
    }
}
