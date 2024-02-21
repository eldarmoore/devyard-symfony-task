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
        $agentId = $options['agent_id']; // Assuming this option is passed when creating the form

        $builder
            ->add('users', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'multiple' => true,
                'expanded' => false,
                'query_builder' => function (EntityRepository $er) use ($agentId) {
                    return $er->createQueryBuilder('u')
                        ->where('u.agentInCharge = :agentId')
                        ->setParameter('agentId', $agentId)
                        ->orderBy('u.username', 'ASC');
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'agent_id' => null, // Ensure a default value is set
        ]);
    }
}
