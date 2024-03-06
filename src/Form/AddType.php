<?php

namespace App\Form;

use App\Entity\Employees;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('EmpName')
            ->add('EmpLastName')
            ->add('EmpSex')
            ->add('EmpEmail')
            ->add('EmpAddress')
            ->add('EmpPhone')
            ->add('EmpFunction')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employees::class,
        ]);
    }
}
