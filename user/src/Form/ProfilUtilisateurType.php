<?php

namespace App\Form;

use App\Entity\ProfilUtilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProfilUtilisateurType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('adresse', TextareaType::class, [
                'label' => 'Adresse',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez votre adresse complète',
                    'maxlength' => 255,
                    'rows' => 3
                ],
                'help' => 'Format: numéro, rue, code postal, ville'
            ])
            ->add('telephone', TelType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '+33 6 12 34 56 78',
                    'pattern' => '^\+?[0-9\s\-\.()]+$',
                    'maxlength' => 20
                ],
                'help' => 'Format: +33612345678 ou 0612345678'
            ])
            ->add('photoProfil', TextType::class, [
                'label' => 'Photo de profil (URL ou chemin)',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'URL ou chemin de votre photo de profil'
                ]
            ])
            // Ne pas ajouter "utilisateur" ici, car c'est la relation OneToOne (on la gère dans le contrôleur)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProfilUtilisateur::class,
        ]);
    }
}
