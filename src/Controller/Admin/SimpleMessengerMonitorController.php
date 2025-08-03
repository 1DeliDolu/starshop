<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/messenger')]
class SimpleMessengerMonitorController extends AbstractController
{
    #[Route('', name: 'admin_messenger_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('admin/messenger_monitor.html.twig');
    }
}
