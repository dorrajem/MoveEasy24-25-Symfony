<?php

namespace App\Form;

use App\Entity\Avis;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AvisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        assert(isset($options['data_class']), "L'option data_class doit être définie");
        assert($options['data_class'] === Avis::class, "La classe de données doit être Avis");
        
        $builder
            ->add('contenu', TextareaType::class, [
                'label' => 'Votre commentaire',
                'attr' => [
                    'placeholder' => 'Partagez votre expérience...',
                    'rows' => 5
                ]
            ])
            ->add('note', IntegerType::class, [
                'label' => 'Note',
                'attr' => [
                    'min' => 1,
                    'max' => 5
                ]
            ])
            ->add('dateAvis', DateType::class, [
                'label' => 'Date de l\'avis',
                'widget' => 'single_text',
                'html5' => true
            ])
            ->add('id_utilisateur', IntegerType::class, [
                'label' => 'ID Utilisateur'
            ])
        ;
        
        assert(count($builder->all()) === 4, "Le formulaire doit contenir exactement 4 champs");
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Avis::class,
        ]);
    }
}