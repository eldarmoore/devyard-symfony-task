<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            ->add('password', PasswordType::class)
            ->add('currency', ChoiceType::class, [
                'choices' => [
                    'USD' => 'USD',
                    'EUR' => 'EUR',
                    'BTC' => 'BTC',
                ],
                'mapped' => false,
            ])
            ->add('agentRole', ChoiceType::class, [
                'choices' => [
                    'Admin' => 'ROLE_ADMIN',
                    'Representative' => 'ROLE_REP',
                ],
                'mapped' => false, // This field does not directly map to an entity property
                'label' => 'Agent Role',
                'attr' => ['class' => 'agent-role-selector'],
            ])
            ->add('accountType', ChoiceType::class, [
                'choices' => [
                    'User' => 'ROLE_USER',
                    'Agent' => 'ROLE_AGENT',
                ],
                'mapped' => false,
                'label' => 'Account Type',
            ])
            ->add('register', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
