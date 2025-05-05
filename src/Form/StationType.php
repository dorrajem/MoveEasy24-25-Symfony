<?php

namespace App\Form;

use App\Entity\itineraire;
use App\Entity\Station;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints as Assert;
class StationType extends AbstractType
{
    
public function buildForm(FormBuilderInterface $builder, array $options): void
{
    $builder
        ->add('nomStation', ChoiceType::class, [
            'choices' => [
                'Bizerte' => 'Bizerte',
                'Sfax' => 'Sfax',
                'Tunis' => 'Tunis',
                'Sousse' => 'Sousse',
                'Nabeul' => 'Nabeul',
                'Tozeur' => 'Tozeur',
                'Gabes' => 'Gabes',
                'Tataouine' => 'Tataouine',
                'Jendouba' => 'Jendouba',
            ],
            'placeholder' => 'Select a City',
            'constraints' => [
                new Assert\NotBlank(message: 'Please select a city.'),
            ],
        ])
        ->add('statut', ChoiceType::class, [
            'choices' => [
                'En Maintenance' => 'En Maintenance',
                'Active' => 'Active',
                'Inactive' => 'Inactive',
            ],
            'placeholder' => 'Select Status',
            'constraints' => [
                new Assert\NotBlank(message: 'Please select a status.'),
            ],
        ])
        ->add('latitude', ChoiceType::class, [
            'choices' => [
                'Bizerte' => 37.2744,
                'Sfax' => 34.7406,
                'Tunis' => 36.8065,
                'Sousse' => 35.8256,
                'Nabeul' => 36.4513,
                'Tozeur' => 33.9197,
                'Gabes' => 33.8815,
                'Tataouine' => 32.9297,
                'Jendouba' => 36.5011,
            ],
            'placeholder' => 'Select Latitude',
            'constraints' => [
                new Assert\NotBlank(message: 'Please select a latitude.'),
            ],
        ])
        ->add('longitude', ChoiceType::class, [
            'choices' => [
                'Bizerte' => 9.8650,
                'Sfax' => 10.7603,
                'Tunis' => 10.1815,
                'Sousse' => 10.6369,
                'Nabeul' => 10.7350,
                'Tozeur' => 8.1335,
                'Gabes' => 10.0972,
                'Tataouine' => 10.4518,
                'Jendouba' => 8.7802,
            ],
            'placeholder' => 'Select Longitude',
            'constraints' => [
                new Assert\NotBlank(message: 'Please select a longitude.'),
            ],
        ])
    ;
}
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Station::class,
        ]);
    }
}
