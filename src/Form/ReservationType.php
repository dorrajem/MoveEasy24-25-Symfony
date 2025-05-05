<?php

namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Check if we're only editing the status (etatR)
        $onlyStatus = $options['only_status'] ?? false;
        
        // Add all fields but make them disabled/read-only except for etatR
        $builder
            ->add('idR', TextType::class, [
                'label' => 'ID',
                'disabled' => true,
                'attr' => ['readonly' => true]
            ])
            ->add('iduser', TextType::class, [
                'label' => 'ID Utilisateur',
                'disabled' => true,
                'attr' => ['readonly' => true]
            ])
            ->add('idTrain', TextType::class, [
                'label' => 'ID Train',
                'disabled' => true,
                'attr' => ['readonly' => true]
            ])
            ->add('idItiner', TextType::class, [
                'label' => 'ID Itinéraire',
                'disabled' => true,
                'attr' => ['readonly' => true]
            ])
            ->add('dateR', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de réservation',
                'required' => true,
                'disabled' => $onlyStatus,
                'attr' => ['readonly' => $onlyStatus]
            ])
            ->add('heureR', TimeType::class, [
                'widget' => 'single_text',
                'label' => 'Heure de réservation',
                'required' => true,
                'disabled' => $onlyStatus,
                'attr' => ['readonly' => $onlyStatus]
            ])
            ->add('etatR', ChoiceType::class, [
                'choices' => [
                    'En attente' => 'En attente',
                    'Confirmée' => 'Confirmée',
                    'Annulée' => 'Annulée',
                ],
                'label' => 'État',
                'required' => true,
                'empty_data' => 'En attente',
                'disabled' => false // Always editable
            ])
            ->add('nbrplace', IntegerType::class, [
                'label' => 'Nombre de places',
                'required' => true,
                'disabled' => $onlyStatus,
                'attr' => ['readonly' => $onlyStatus]
            ])
            ->add('typeplace', ChoiceType::class, [
                'choices' => [
                    'Standard' => 'Standard',
                    'Premium' => 'Premium',
                    'Business' => 'Business',
                ],
                'label' => 'Type de place',
                'required' => true,
                'empty_data' => 'Standard',
                'disabled' => $onlyStatus,
                'attr' => ['readonly' => $onlyStatus]
            ])
            ->add('typeTrainR', ChoiceType::class, [
                'choices' => [
                    'TGV' => 'TGV',
                    'TER' => 'TER',
                    'Intercités' => 'Intercités',
                ],
                'label' => 'Type de train',
                'required' => true,
                'empty_data' => 'TGV',
                'disabled' => $onlyStatus,
                'attr' => ['readonly' => $onlyStatus]
            ])
            ->add('destinationR', ChoiceType::class, [
                'choices' => [
                    'Tunis-Sousse' => 'Tunis-Sousse',
                    'Tunis-Sfax' => 'Tunis-Sfax',
                    'Tunis-Keff' => 'Tunis-Keff',
                ],
                'label' => 'Destination',
                'required' => true,
                'disabled' => $onlyStatus,
                'attr' => ['readonly' => $onlyStatus]
            ])
            ->add('NameUser', TextType::class, [
                'label' => 'Nom d\'utilisateur',
                'required' => true,
                'disabled' => $onlyStatus,
                'attr' => ['readonly' => $onlyStatus]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
            'only_status' => true, // Default to only allowing status edits
        ]);
    }
}