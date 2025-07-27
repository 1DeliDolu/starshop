<?php
namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CsrfHeaderListener
{
    private $csrfTokenManager;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->csrfTokenManager = $csrfTokenManager;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        if ($request->headers->has('X-CSRF-TOKEN')) {
            $token = $request->headers->get('X-CSRF-TOKEN');
            $csrfToken = new CsrfToken('submit', $token);
            if (!$this->csrfTokenManager->isTokenValid($csrfToken)) {
                throw new AccessDeniedHttpException('Invalid CSRF token');
            }
        }
    }
}
