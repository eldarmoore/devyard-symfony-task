<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;

class SessionExpirationListener
{
    private $router;
    private $security;
    private $sessionMaxTime;

    public function __construct(UrlGeneratorInterface $router, Security $security, int $sessionMaxTime)
    {
        $this->router = $router;
        $this->security = $security;
        $this->sessionMaxTime = $sessionMaxTime;
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        $user = $this->security->getUser();

        if (!$user) {
            return; // Do nothing if no user is logged in
        }

        $session = $event->getRequest()->getSession();
        $sessionStartTime = $session->get('session_start_time');
        if (!$sessionStartTime) {
            return; // Session start time is not set
        }

        $currentTime = time();
        $timeElapsed = $currentTime - $sessionStartTime;
        $sessionMaxTime = $this->sessionMaxTime; // Access the parameter
        $remainingLifetime = max($sessionMaxTime - $timeElapsed, 0);

        if ($remainingLifetime <= 0) {
            $session->invalidate(); // Invalidate the session
            $response = new RedirectResponse($this->router->generate('login')); // Redirect to login page
            $event->setResponse($response);
        }
    }
}
