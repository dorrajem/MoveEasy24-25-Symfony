<?php

namespace App\Form;

use App\Entity\Train;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrainType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('TypeTrain', TextType::class, [
                'label' => 'Type de train',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex : TGV, Métro, TER...',
                    'minlength' => 2,
                    'maxlength' => 255
                ]
            ])
            ->add('capacite', IntegerType::class, [
                'label' => 'Capacité',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                    'step' => 1,
                    'placeholder' => 'Ex : 200'
                ]
            ])
            ->add('statut', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'En service' => 'En service',
                    'Hors service' => 'Hors service',
                    'En maintenance' => 'En maintenance',
                ],
                'placeholder' => 'Choisissez un statut',
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('dateMiseEnService', DateType::class, [
                'label' => 'Date de mise en service',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                    'max' => (new \DateTime())->format('Y-m-d'), // ✅ No future dates allowed
                    'placeholder' => 'Sélectionnez une date'
                ],
                'html5' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Train::class,
        ]);
    }
}
