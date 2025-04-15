<?php
// src/Form/ReclamationType.php
namespace App\Form;

use App\Entity\Reclamation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextareaType::class, [
                'label' => 'Description du problème',
                'attr' => [
                    'placeholder' => 'Veuillez décrire votre problème en détail...',
                    'rows' => 5,
                    'class' => 'form-control'
                ],
                'help' => 'Minimum 10 caractères, maximum 255 caractères',
                'constraints' => [
                    new NotBlank(['message' => 'La description ne peut pas être vide']),
                    new Length([
                        'min' => 10,
                        'max' => 255,
                        'minMessage' => 'La description doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('categorie', ChoiceType::class, [
                'label' => 'Catégorie',
                'choices' => [
                    'Retard' => 'Retard',
                    'Annulation' => 'Annulation',
                    'Confort' => 'Confort',
                    'Service' => 'Service',
                    'Autre' => 'Autre'
                ],
                'attr' => ['class' => 'form-select'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner une catégorie'])
                ]
            ]);
            
        // Seuls les admins peuvent modifier le statut
        if ($options['is_admin'] ?? false) {
            $builder->add('statut', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'Nouvelle' => 'Nouvelle',
                    'En cours' => 'En cours',
                    'Traitée' => 'Traitée',
                    'Clôturée' => 'Clôturée',
                    'En attente' => 'En attente'
                ],
                'attr' => ['class' => 'form-select'],
                'constraints' => [
                    new NotBlank(['message' => 'Le statut ne peut pas être vide'])
                ]
            ]);
            
            // Add response field for admins only
            $builder->add('reponse', TextareaType::class, [
                'label' => 'Réponse',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Entrez une réponse à cette réclamation...',
                    'rows' => 4,
                    'class' => 'form-control'
                ],
                'help' => 'Maximum 1000 caractères',
                'constraints' => [
                    new Length([
                        'max' => 1000,
                        'maxMessage' => 'La réponse ne peut pas dépasser {{ limit }} caractères'
                    ])
                ]
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
            'is_admin' => false,
        ]);
    }
}