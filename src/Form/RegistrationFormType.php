<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom', TextType::class, [
            "label" => "Nom",
            'attr' => [
                'class' => 'form-control',
                "placeholder" => "Votre Nom"
            ],
        ])
        ->add('prenom', TextType::class, [
            "label" => "Prénom",
            'attr' => [
                'class' => 'form-control',
                "placeholder" => "Votre Prénom"
            ],
        ])
        ->add('age', NumberType::class, [
            "label" => "Age",
            'attr' => [
                'class' => 'form-control',
                "placeholder" => "Votre Age"
            ],
        ])
        ->add('email', EmailType::class, [
            'attr' => [
                'class' => 'form-control',
                "placeholder" => "Votre email"
            ],
        ])
        ->add('telephone', NumberType::class, [
            "label" => "telephone",
            'attr' => [
                'class' => 'form-control',
                "placeholder" => "Votre telephone"
            ],
        ])
        ->add('adresse', TextType::class, [
            "label" => "adresse",
            'attr' => [
                'class' => 'form-control',
                "placeholder" => "Votre adresse"
            ],
        ])
        ->add('cp', NumberType::class, [
            "label" => "Code postal",
            'attr' => [
                'class' => 'form-control',
                "placeholder" => "Votre Code postal"
            ],
        ])
        ->add('pays', TextType::class, [
            "label" => "pays",
            'attr' => [
                'class' => 'form-control',
                "placeholder" => "Votre pays"
            ],
        ])
        ->add('pdp', FileType::class, [
            "label" => "Photo de profil",
            'attr' => [
                'class' => 'form-control',
                "placeholder" => "Votre Photo de profil"
            ],
        ])
        
        ->add('password', RepeatedType::class, [
            // instead of being set onto the object directly,
            // this is read and encoded in the controller
            'type' => PasswordType::class,
            'invalid_message' => 'The password fields must match.',
            'options' => ['attr' => [
                'autocomplete' => 'new-password',
                'class' => 'form-control',
                "placeholder" => "Votre mot de passe"
            ]],
            'first_options'  => ['label' => 'Mot de passe'],
            'second_options' => ['label' => 'Confirmer mot de passe'],
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter a password',
                ]),
                new Length([
                    'min' => 6,
                    'minMessage' => 'Your password should be at least {{ limit }} characters',
                    // max length allowed by Symfony for security reasons
                    'max' => 4096,
                ]),
            ],
            'mapped'=>false
        ])
        ->add('roles', ChoiceType::class, [
            'required' => true,
            'multiple' => false,
            'expanded' => false,
            "label" => "Choisir le rôle :",
            'attr' => [
                'class' => 'form-control',
            ],
            'choices'  => [
                'Utilisateur' => 'ROLE_USER',
                'Editeur' => 'ROLE_EDIT',
                'Admin' => 'ROLE_ADMIN',
            ],
        ])
        ->add('submit', SubmitType::class, [
            "label" => "Valider",
            "attr" => [
                "class" => "btn btn-success"
            ]
        ]);

    $builder->get('roles')
        ->addModelTransformer(new CallbackTransformer(
            function ($rolesArray) {
                // transform the array to a string
                return count($rolesArray) ? $rolesArray[0] : null;
            },
            function ($rolesString) {
                // transform the string back to an array
                return [$rolesString];
            }
        ));
}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
