<?php

namespace App\Form;

use App\Entity\Reclamation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('categorie', ChoiceType::class, [
                'label' => 'Catégorie',
                'choices' => [
                    'Problème technique' => 'technique',
                    'Facturation' => 'facturation',
                    'Service client' => 'service_client',
                    'Suggestion' => 'suggestion',
                    'Autre' => 'autre'
                ],
                'placeholder' => 'Choisissez une catégorie',
                'required' => true
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Décrivez votre problème ou votre requête...',
                    'rows' => 5
                ],
                'required' => true
            ]);
            
        // Ajouter le champ statut seulement pour les admins
        if (isset($options['is_admin']) && $options['is_admin']) {
            $builder->add('statut', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'En attente' => 'en attente',
                    'En cours' => 'en cours',
                    'Traitée' => 'traitée'
                ],
                'required' => true
            ])
            ->add('reponse', TextareaType::class, [
                'label' => 'Réponse',
                'attr' => [
                    'placeholder' => 'Réponse à la réclamation...',
                    'rows' => 3
                ],
                'required' => false
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
            'is_admin' => false,
        ]);
        
        $resolver->setAllowedTypes('is_admin', 'bool');
    }
}