<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', TextType::class, [
            'label' => false,
            'attr'=> ['name' => '_username'],
            'mapped' => false, // Ne pas lier ce champ à une propriété de l'entité
            'data' => $options['last_username'] ?? '' // Récupérer le dernier email saisi
        ])
        ->add('password', PasswordType::class, [
            'label' => false,
            'attr'=> ['name' => '_password'],
            'mapped' => false, // Ne pas lier ce champ à une propriété de l'entité
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'last_username' => null, // Permet de préremplir le champ email
            'csrf_protection' => true,
        ]);
    }
}
