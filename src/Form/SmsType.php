<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SmsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('number', TextType::class, [
                'label' => 'Numéro de téléphone:',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('name', TextType::class, [
                'label' => "Nom de l'expéditeur:",
                'attr' => ['class' => 'form-control'],
            ])
            ->add('text', TextareaType::class, [
                'label' => 'Text:',
                'attr' => ['class' => 'form-control'],
            ]);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'companyName' => null,
            'phoneNumber' => null,
        ]);
    }
}
