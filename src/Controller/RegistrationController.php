<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Agent;
use App\Form\UserRegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserRegistrationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $accountType = $form->get('accountType')->getData();

            if ($accountType === 'ROLE_AGENT') {
                $entity = new Agent();
                $agentRole = $form->get('agentRole')->getData();
                $entity->setRole($agentRole);
            } else {
                $entity = new User();
                $currency = $form->get('currency')->getData();
                $entity->setCurrency($currency);
            }

            $entity->setUsername($form->get('username')->getData());

            // Hash the password
            $hashedPassword = $passwordHasher->hashPassword($entity, $form->get('password')->getData());
            $entity->setPassword($hashedPassword);

            // Save entity
            $entityManager->persist($entity);
            $entityManager->flush();

            // Redirect to login page
            return $this->redirectToRoute('login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
