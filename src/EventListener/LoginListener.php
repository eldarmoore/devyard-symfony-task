<?php

namespace App\EventListener;

use App\Entity\Agent;
use App\Entity\Log;
use App\Entity\User;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Doctrine\ORM\EntityManagerInterface;

class LoginListener
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $entity = $event->getAuthenticationToken()->getUser();

        // Log the login action
        $log = new Log();
        $log->setActionName('login');
        $log->setDateCreated(new \DateTime());
        // Check the type of entity and set the appropriate association
        if ($entity instanceof User) {
            $entity->setLoginTime(new \DateTime());
            $log->setUser($entity);
        } elseif ($entity instanceof Agent) {
            $entity->setLoginTime(new \DateTime());
            $log->setAgent($entity);
        }

        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }
}
