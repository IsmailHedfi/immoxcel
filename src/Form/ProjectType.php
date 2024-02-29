<?php

namespace App\Form;

use App\Entity\Project;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('projectName', null, [
                'empty_data' => '',
            ])
            ->add('date_pred_start', DateType::class, [], null, [
                'empty_data' => '',
            ])
            ->add('date_pred_finish', DateType::class, [], null, [
                'empty_data' => '',
            ])
            ->add('date_completion', DateType::class, [], null, [
                'empty_data' => '',
            ])
            ->add('budget', null, [
                'empty_data' => '',
            ])
            ->add('actual_cost', null, [
                'empty_data' => '',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
