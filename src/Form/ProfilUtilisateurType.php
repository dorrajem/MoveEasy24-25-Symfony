<?php

namespace App\Form;

use App\Entity\ProfilUtilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfilUtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('adresse', TextareaType::class, [
                'required' => false,
                'attr'     => ['rows' => 3, 'class' => 'form-control'],
            ])
            ->add('telephone', TelType::class, [
                'required' => false,
                'attr'     => ['class' => 'form-control'],
            ])
            ->add('photoProfilFile', FileType::class, [
                'label'    => 'Avatar (jpg/png)',
                'required' => false,
                'mapped'   => true,      // ← must match property name!
                'attr'     => ['accept' => 'image/*'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProfilUtilisateur::class,
        ]);
    }
}
