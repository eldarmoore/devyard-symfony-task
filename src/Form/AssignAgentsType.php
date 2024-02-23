<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Agent;
use Doctrine\ORM\EntityRepository;

class AssignAgentsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('agents', EntityType::class, [
                'class' => Agent::class,
                'choice_label' => 'username', // Display agent usernames in the dropdown
                'multiple' => true,
                'expanded' => false,
                // Optionally, add a query builder to filter agents
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('a')
                        ->andWhere('a.agentInCharge IS NULL')
                        ->orderBy('a.username', 'ASC');
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // No need to set any default options
        ]);
    }
}
