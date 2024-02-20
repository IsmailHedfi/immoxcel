<?php

namespace App\Form;

use App\Entity\Materials;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class MaterialsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('TypeMaterial',TextType::class,['empty_data' => ''])
            ->add('UnitPrice',NumberType::class,['empty_data' => ''])
            ->add('Quantity',IntegerType::class,['empty_data' => ''])
            ->add('Depot',EntityType::class, 
            ['class'=>'App\Entity\Depot',
            'choice_label'=>function ($Depot) {
                return $Depot->getLocation();
            }])
            ->add('Submit',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Materials::class,
        ]);
    }
}
