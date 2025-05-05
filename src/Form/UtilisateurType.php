<?php

namespace App\Form;

use App\Entity\Utilisateur;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr'  => [
                    'class'       => 'form-control',
                    'placeholder' => 'Entrez votre nom',
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'attr'  => [
                    'class'       => 'form-control',
                    'placeholder' => 'Entrez votre prénom',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr'  => [
                    'class'       => 'form-control',
                    'placeholder' => 'Entrez votre email',
                ],
            ])
            ->add('role', ChoiceType::class, [
                'label'   => 'Rôle',
                'choices' => [
                    'Admin'   => 'Admin',
                    'Employé' => 'Employé',
                ],
                'attr'  => [
                    'class' => 'form-control',
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label'       => 'Mot de passe',
                'mapped'      => false,
                'required'    => $options['require_password'],
                'attr'        => [
                    'class'        => 'form-control',
                    'placeholder'  => 'Entrez le mot de passe',
                    'autocomplete' => 'new-password',
                ],
                'constraints' => $options['require_password'] ? [
                    new NotBlank([
                        'message' => 'Le mot de passe est obligatoire',
                    ]),
                    new Length([
                        'min'        => 4,
                        'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères',
                    ]),
                ] : [],
            ])
            // Champ reCAPTCHA v2 (checkbox)
            ->add('captcha', EWZRecaptchaType::class, [
                'mapped'      => false,
                'constraints' => [
                    new RecaptchaTrue([
                        'message' => 'La vérification anti-robot a échoué, réessayez.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'       => Utilisateur::class,
            'require_password' => true,
        ]);
    }
}
