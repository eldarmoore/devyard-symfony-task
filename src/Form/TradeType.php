<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use App\Entity\Trade;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Repository\UserRepository;

class TradeType extends AbstractType
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lotCount', NumberType::class, [
                'label' => 'Lot Count',
                'required' => true,
                'attr' => ['min' => 0.1, 'max' => 100, 'step' => 0.1],
            ])
            ->add('position', ChoiceType::class, [
                'choices' => [
                    'Buy' => 'buy',
                    'Sell' => 'sell',
                ],
                'label' => 'Position',
            ]);

        if ($options['is_agent']) {
            $builder->add('user', EntityType::class, [
                'class' => User::class,
                // Directly pass the Agent entity to the repository method
                'choices' => $this->userRepository->findUsersUnderAgent($options['agent'])->getQuery()->getResult(),
                'choice_label' => 'username',
                'placeholder' => 'Select a user',
                'required' => false,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trade::class,
            'is_agent' => false,
            'agent_id' => null,
            'agent' => null,
            // Define the user_currency option
            'user_currency' => null, // Default value or you could make it required without a default
        ]);
    }
}
