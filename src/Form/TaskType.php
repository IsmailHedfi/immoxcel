<?php

namespace App\Form;

use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('taskTitle', null, [
                'empty_data' => '',
            ])
            ->add('taskDescription')
            ->add('taskDeadline')
            ->add('taskStatus',ChoiceType::class,[
                'choices' => [
                    'To Do' => 'To Do'
                    ,'Doing' => 'Doing',
                    'Done' => 'Done',
                ]        , 
                ])
            ->add('taskCompletionDate')
            ->add('project')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
