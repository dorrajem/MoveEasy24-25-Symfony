<?php

namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class FrontReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('dateR', DateType::class, [
            'widget' => 'single_text',
            'label' => 'Date de réservation',
            'required' => true,  // Keep this
            'attr' => [
                'min' => (new \DateTime())->format('Y-m-d'),
                'class' => 'form-control'
            ],
            'row_attr' => ['class' => 'form-group'],
            'error_bubbling' => false,  // This ensures errors appear at the field level
            'constraints' => [
                new \Symfony\Component\Validator\Constraints\NotNull([
                    'message' => 'La date est obligatoire'
                ])
            ]
        ])
        ->add('heureR', TimeType::class, [
            'widget' => 'single_text',
            'label' => 'Heure de réservation',
            'required' => true,
            'attr' => ['class' => 'form-control'],
            'row_attr' => ['class' => 'form-group'],
            'error_bubbling' => false,
            'constraints' => [
                new \Symfony\Component\Validator\Constraints\NotNull([
                    'message' => 'L\'heure est obligatoire'
                ])
                ]
                ])
            ->add('nbrplace', IntegerType::class, [
                'label' => 'Nombre de places',
                'attr' => [
                    'min' => 1,
                    'max' => 255,
                    'class' => 'form-control'
                ],
                'row_attr' => ['class' => 'form-group']
            ])
            ->add('typeplace', ChoiceType::class, [
                'label' => 'Type de place',
                'choices' => [
                    'Handicap' => 'Handicap',
                    'Première Classe' => 'Première Classe',
                    'Économie ' => 'Économie'
                ],
                'attr' => ['class' => 'form-control'],
                'row_attr' => ['class' => 'form-group']
            ])
            ->add('typeTrainR', ChoiceType::class, [
                'label' => 'Type de train',
                'choices' => [
                    'Express' => 'Express',
                    'Régional' => 'Regional',
                    'TGV' => 'TGV'
                ],
                'attr' => ['class' => 'form-control'],
                'row_attr' => ['class' => 'form-group']
            ])
            ->add('destinationR', ChoiceType::class, [
                'label' => 'Destination',
                'choices' => [
                    'Tunis-Sousse' => 'Tunis-Sousse',
                    'Tunis-Sfax' => 'Tunis-Sfax',
                    'Tunis-Keff' => 'Tunis-Keff'
                ],
                'attr' => ['class' => 'form-control'],
                'row_attr' => ['class' => 'form-group']
            ])
            ->add('methodeP', ChoiceType::class, [
                'label' => 'Méthode de paiement',
                'mapped' => false, // This field is not mapped to Reservation entity
                'choices' => [
                    'Espèces' => 'Espèces',
                    'Carte bancaire' => 'Carte bancaire'
                ],
                'attr' => ['class' => 'form-control'],
                'row_attr' => ['class' => 'form-group']
            ]);
            
        // Add event listener to validate fields
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();
            
            // Check dateR field
            if ($data->getDateR() === null) {
                $form->get('dateR')->addError(new FormError('La date est obligatoire'));
            }
            
            // Check heureR field
            if ($data->getHeureR() === null) {
                $form->get('heureR')->addError(new FormError('L\'heure est obligatoire'));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
            'attr' => ['novalidate' => 'novalidate'], // Disable HTML5 validation to ensure our validation runs
        ]);
    }
}