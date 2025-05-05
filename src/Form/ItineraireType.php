<?php

namespace App\Form;

use App\Entity\Itineraire;
use App\Entity\Station;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
class ItineraireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomItineraire', TextType::class, [
                'attr' => [
                    'placeholder' => 'Start - End',  // Dash visible in placeholder
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => '/^.+\s-\s.+$/',
                        'message' => 'Format must be "Start - End"',
                    ]),
                ],
            ])
            /*->add('distance', NumberType::class, [
                'scale' => 2,
                'attr' => [
                    'min' => 0,
                    'step' => 0.01, // Allow decimals
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Positive([
                        'message' => 'Distance must be a positive number.',
                    ]),
                ],
            ])*/
            ->add('heureDepartPrevue', TimeType::class, [
                'widget' => 'single_text',
                
            ])
            ->add('heureArriveePrevue', TimeType::class, [
                'widget' => 'single_text',
                
            
            ])
            ->add('dateiti', DateType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\GreaterThan('today', message: 'The date must be after today.'),
                ],
            ])
            ->add('startStation', EntityType::class, [
                'class' => Station::class,
                'choice_label' => 'nomStation',
                'placeholder' => 'Select Start Station',
                'constraints' => [
                    new Assert\NotNull(message: 'Please select a starting station.'),
                ],
            ])
            ->add('endStation', EntityType::class, [
                'class' => Station::class,
                'choice_label' => 'nomStation',
                'placeholder' => 'Select End Station',
                'constraints' => [
                    new Assert\NotNull(message: 'Please select an ending station.'),
                ],
            ])
            ->add('distance', NumberType::class, [
                'label' => 'Distance (km)',
                'required' => false,
                'attr' => ['readonly' => true] // or 'disabled' => true
            ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Itineraire::class,
        ]);
    }
}
