<?php

namespace App\EventListener;

use App\Entity\Agent;
use App\Entity\User;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Log;

class LogoutListener
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function onLogoutEvent(LogoutEvent $event)
    {
        $entity = $event->getToken()->getUser();

        if ($entity) {
            $log = new Log();
            $log->setActionName('logout');
            $log->setDateCreated(new \DateTime());

            // Check the type of entity and set the appropriate association
            if ($entity instanceof User) {
                $log->setUser($entity);
            } elseif ($entity instanceof Agent) {
                $log->setAgent($entity);
            }

            $this->entityManager->persist($log);
            $this->entityManager->flush();
        }
    }
}
