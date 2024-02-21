<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;

class AssignUsersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('users', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'multiple' => true, // Allow selection of multiple users
                'expanded' => false, // Use a select dropdown; set to true for checkboxes
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.agentInCharge IS NULL') // Only include users not already assigned to an agent
                        ->orderBy('u.username', 'ASC');                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your default options here
        ]);
    }
}
