<?php

namespace App\Service;

use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    public function updateLoginTime(User $user): void
    {
        // Set the login time to the current date and time
        $loginTime = new DateTime();
        $user->setLoginTime($loginTime);

        // Persist the changes to the database
        $this->entityManager->flush();
    }
}
