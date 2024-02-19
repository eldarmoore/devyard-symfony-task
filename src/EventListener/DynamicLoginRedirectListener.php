<?php

namespace App\EventListener;

use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class DynamicLoginRedirectListener
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onLoginSuccess(LoginSuccessEvent $event)
    {
        $user = $event->getAuthenticatedToken()->getUser();
        $roles = $user->getRoles();

        // Define your logic to determine the target path
        if (in_array('ROLE_AGENT', $roles)) {
            $redirectUrl = $this->router->generate('agent_profile');
        } else {
            // Default redirect for ROLE_USER and any other roles
            $redirectUrl = $this->router->generate('user_profile');
        }

        $response = new RedirectResponse($redirectUrl);
        $event->setResponse($response);
    }
}
