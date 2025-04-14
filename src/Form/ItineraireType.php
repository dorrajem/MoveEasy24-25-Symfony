<?php

namespace App\Form;

use App\Entity\Itineraire;
use App\Entity\Station;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItineraireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomItineraire')
            ->add('distance')
            ->add('heureDepartPrevue', null, [
                'widget' => 'single_text',
            ])
            ->add('heureArriveePrevue', null, [
                'widget' => 'single_text',
            ])
            ->add('dateiti', null, [
                'widget' => 'single_text',
            ])
            ->add('stat', EntityType::class, [
                'class' => Station::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Itineraire::class,
        ]);
    }
}
