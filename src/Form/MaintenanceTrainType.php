<?php

namespace App\Form;

use App\Entity\MaintenanceTrain;
use App\Entity\Train;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MaintenanceTrainType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateMaintenance', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de maintenance',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'min' => (new \DateTime())->format('Y-m-d'), // HTML5 min date
                    'placeholder' => 'Sélectionnez une date'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description de la maintenance',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Ex. : Changement de freins, vérification moteur...'
                ]
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => $options['status_options'],
                'label' => 'Statut de la maintenance',
                'required' => true,
                'attr' => [
                    'class' => 'form-select'
                ]
            ]);

        // Optional: Only show train selector if not predefined
        }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MaintenanceTrain::class,
            'train_predefined' => false,
            'status_options' => [
                'Planifiée' => 'Planifiée',
                'En cours' => 'En cours',
                'Terminée' => 'Terminée',
            ],
        ]);
    }
}
