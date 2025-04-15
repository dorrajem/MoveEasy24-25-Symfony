<?php

namespace App\Form;

use App\Entity\MaintenanceEq;
use App\Entity\Equipement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MaintenanceEqType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('idEquipement', EntityType::class, [
            'class' => Equipement::class,
            'choice_label' => function (Equipement $eq) {
                return $eq->getTypeEquipement() . ' - Train #' . $eq->getIdTrain();
            },
            'placeholder' => 'Choisir un équipement',
            'label' => 'Équipement concerné',
        ])
            
            ->add('description', TextType::class, [
                'required' => false,
                'label' => 'Description',
            ])
            ->add('periode', TextType::class, [
                'required' => false,
                'label' => 'Période',
            ])
            ->add('dateMaintenance', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'label' => 'Date de maintenance',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MaintenanceEq::class,
        ]);
    }
}
