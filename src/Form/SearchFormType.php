<?php

namespace App\Form;

use App\Data\SearchData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', TextType::class, [
                'required' => false,
                'label' => 'Type d\'équipement',
                'attr' => ['placeholder' => 'Entrez le type d\'équipement']
            ])
            ->add('statut', ChoiceType::class, [
                'required' => false,
                'label' => 'Statut',
                'choices' => [
                    'disponible' => 'disponible',
                    'non disponible' => 'non disponible',
                    
                ],
                'placeholder' => 'Sélectionnez un statut',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}
