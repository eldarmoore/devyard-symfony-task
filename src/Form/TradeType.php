<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

class TradeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lotCount', NumberType::class, [
                'label' => 'Lot Count',
                'required' => true,
                'attr' => [
                    'min' => 0.1,
                    'max' => 100,
                    'step' => 0.1,
                ],
            ])
            ->add('position', ChoiceType::class, [
                'label' => 'Position',
                'choices' => [
                    'Buy' => 'buy',
                    'Sell' => 'sell',
                ],
                'required' => true,
            ]);
    }
}
