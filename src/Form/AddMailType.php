<?php

namespace App\Form;

use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddMailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('senderEmail', HiddenType::class, [
            'data' => 'chiboub.ghalia@gmail.com', // Set the default value
        ])
        ->add('recipientEmail', EmailType::class, [
            'label' => 'Recipient\'s Email',
        ])
        ->add('subject', null, [
            'empty_data' => '', // Default value for EmpName if left empty
        ])
        ->add('message', TextareaType::class, [
            'label' => 'Message',
            'attr' => ['rows' => 5],
        ])
        ->add('save', SubmitType::class);
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
