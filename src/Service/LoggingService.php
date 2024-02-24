<?php

namespace App\Service;

use App\Entity\Log;
use Doctrine\ORM\EntityManagerInterface;

class LoggingService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function logTradeCreation($user): void
    {
        // Create a Log entity for trade creation
        $log = new Log();
        $log->setActionName('Trade created');
        $log->setUser($user);

        // Persist the log entity
        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }

    public function logUserAssignment($agent): void
    {
        // Create a Log entity for user assignment
        $log = new Log();
        $log->setActionName('User assigned to agent');
        $log->setAgent($agent);

        // Persist the log entity
        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }

}
