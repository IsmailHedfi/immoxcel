<?php

namespace App\Form;

use App\Entity\Employees;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;


class AddEmployeeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('EmpName', null, [
                'empty_data' => '', // Default value for EmpName if left empty
            ])
            ->add('EmpLastName', null, [
                'empty_data' => '', // Default value for EmpName if left empty
            ])
            ->add('EmpSex', ChoiceType::class, [
                'choices' => [
                    '--' => '',
                    'Female' => 'Female',
                    'Male' => 'Male',
                ],
            ], null, [
                'empty_data' => '', // Default value for EmpName if left empty
            ])
            ->add('EmpEmail', null, [
                'empty_data' => '', // Default value for EmpName if left empty
            ])
            ->add('EmpAddress', null, [
                'empty_data' => '', // Default value for EmpName if left empty
            ])
            ->add('EmpPhone', null, [
                'empty_data' => '', // Default value for EmpName if left empty
            ])
            ->add('EmpFunction', ChoiceType::class, [
                'choices' => [
                    '--' => '',
                    'Employee' => 'Employee',
                    'HR_Manager' => 'HR_Manager',
                    'HR_Agent' => 'HR_Agent',
                    'Production_Manager' => 'Production_Manager',
                    'Production_Agent' => 'Production_Agent',
                    'Inventory_Manger' => 'Inventory_Manger',
                    'Inventory_Agent' => 'Inventory_Agent',
                    'Financial_Manager' => 'Financial_Manager',
                    'Financial_Agent' => 'Financial_Agent',
                ],
            ], null, [
                'empty_data' => '', // Default value for EmpName if left empty
            ])
            ->add('birthDate', BirthdayType::class, [
                'placeholder' => [
                    'year' => 'Year', 'month' => 'Month', 'day' => 'Day',
                ],
                'label' => 'Birthday label that you want to show',
            ], null, [
                'empty_data' => '', // Default value for EmpName if left empty
            ])
            
            ->add('hireDate', null, [
                'empty_data' => '', // Default value for EmpName if left empty
            ], DateType::class, [
                'html5' => false,
                'year'=>range(date('Y')-50,date('Y')+10)
            ])

            ->add('endContractDate', null, [
                'empty_data' => '', // Default value for EmpName if left empty
            ])
            ->add('contractType', ChoiceType::class, [
                'choices' => [
                    '--' => '',
                    'CIVP' => 'CIVP',
                    'CSC' => 'CSC',
                    'KARAMA' => 'KARAMA',
                    'CDI' => 'CDI',
                    'Autre' => 'Autre',
                ],
            ], null, [
                'empty_data' => '', // Default value for EmpName if left empty
            ])
            ->add('Empcv',FileType::class, [
                'label' => 'Upload CV',]
                , null, [
                'empty_data' => '', // Default value for EmpName if left empty
            ])
            ->add('allowedLeaveDays',HiddenType::class, [
                'empty_data' => 0, // Default value for EmpName if left empty
            ], null, [
                'empty_data' => 0, // Default value for EmpName if left empty
            ])
            ->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employees::class,
        ]);
    }
}
