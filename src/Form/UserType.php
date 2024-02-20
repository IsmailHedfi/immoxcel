<?php

namespace App\Form;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\User;
use App\Entity\Employees;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Repository\EmployeesRepository;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Employee',EntityType::class, 
            ['class'=>'App\Entity\Employees',
            'choice_label'=>function ($employee) {
                return $employee->getEmpName() . ' ' . $employee->getEmpLastName();
            },
            'query_builder' => function (EmployeesRepository $er) {
                // Custom query to select only employees that are not associated with a user
                return $er->createQueryBuilder('e')
                ->leftJoin('App\Entity\User', 'u', 'WITH', 'e = u.Employee')
                ->where('u.id IS NULL');
            },
            ])
            
            ->add('Username')
            ->add('Password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['attr' => ['placeholder' => 'Password']],
                'second_options' => ['attr' => ['placeholder' => 'Confirm Password']],
                
            ])
            ->add('Add',SubmitType::class)
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            
            'data_class' => User::class,
        ]);
    }
}
