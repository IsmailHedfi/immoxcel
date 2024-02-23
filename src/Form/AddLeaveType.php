<?php

namespace App\Form;

use App\Entity\Leaves;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Employees;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;


class AddLeaveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Status',HiddenType::class, [], ChoiceType::class, [
                'choices' => [
                    'Pending'=>'Pending',
                    'Approved' => 'Approved',
                    'Disapproved' => 'Disapproved',
                ]],null, [
                    'empty_data' => '', // Default value for EmpName if left empty
                ]
            )
            ->add('StartDate', null, [
                'empty_data' => '', // Default value for EmpName if left empty
            ])
            ->add('FinishDate', null, [
                'empty_data' => '', // Default value for EmpName if left empty
            ])
            ->add('LeaveDescription', null, [
                'empty_data' => '', // Default value for EmpName if left empty
            ])
            ->add('LeaveType', ChoiceType::class, [
                'choices' => [
                    '--'=>'',
                    'Sick' => 'Sick',
                    'Maternity' => 'Maternity',
                    'Paid' => 'Paid',
                    'Unpaid' => 'Unpaid',
                    'Exceptional' => 'Excptional',
                ],
            ], null, [
                'empty_data' => '', // Default value for EmpName if left empty
            ])
            ->add('Employee', EntityType::class, [
                'class' => Employees::class,
                'choice_label' => function ($employee) {
                    // Customize how the employee entity is displayed as a choice
                    return $employee->getID(); // Assuming there's a method to get the employee's full name
                },
                'attr' => [
                    'style' => 'display: none;', // Hide the field
                ],
                'label_attr' => [
                    'style' => 'display: none;', // Hide the label
                ],  
                // You can also add other options as needed
            ],null, [
                'empty_data' => '', // Default value for EmpName if left empty
            ] )
            ->add('save',SubmitType::class,[
                'label' => 'ADD'
                ])
                ->add('save',SubmitType::class,[
                    'label' => 'ADD'
                    ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Leaves::class,
        ]);
    }
}
