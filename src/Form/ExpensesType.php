<?php

namespace App\Form;

use App\Entity\Expenses;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExpensesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Type')
            ->add('QuantityE')
            ->add('coast')
            ->add('Description')
            ->add('materials', EntityType::class, [
                'class' => 'App\Entity\Materials',
                'choice_label' => 'Type', // Assuming 'type' is the property you want to display
                'attr' => ['class' => 'form-control'],
            ]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Expenses::class,
        ]);
    }
}
