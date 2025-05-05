<?php

namespace App\Form;

use App\Entity\Equipement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class EquipementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('idTrain', IntegerType::class, [
                'required' => false,
                'label' => 'ID Train',
            ])
            ->add('typeEquipement', TextType::class, [
                'required' => false,
                'label' => 'Type d\'équipement',
            ])
            ->add('quantiteDisponible', IntegerType::class, [
                'required' => false,
                'label' => 'Quantité disponible',
            ])
            ->add('statut', TextType::class, [
                'required' => false,
                'label' => 'Statut',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipement::class,
        ]);
    }
}


