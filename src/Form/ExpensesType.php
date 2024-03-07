<?php

namespace App\Form;

use App\Entity\Expenses;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\ProjectsRepository;
use SebastianBergmann\CodeCoverage\Report\Xml\Project;

class ExpensesType extends AbstractType
{
    private $projectsRepository;

    public function __construct(ProjectsRepository $projectsRepository)
    {
        $this->projectsRepository = $projectsRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Type', ChoiceType::class, [
                'choices' => [
                    'Income' => 'Income',
                    'Salary' => 'Salary',
                    'Expenses' => 'Expenses'
                ]
            ])
            ->add('QuantityE')
            ->add('coast')
            ->add('Description')
            ->add('product', EntityType::class, [
                'class' => 'App\Entity\Products',
                'choice_label' => 'Type', // Assuming 'Type' is the property you want to display
                'attr' => ['class' => 'form-control'],
            ])
            ->add('supplier', EntityType::class, [
                'class' => 'App\Entity\Supplier',
                'choice_label' => 'CompanyName', // Assuming 'CompanyName' is the property you want to display
                'attr' => ['class' => 'form-control'],
            ])
            ->add('project', EntityType::class, [
                'class' => 'App\Entity\Projects',
                'choice_label' => 'ProjectName', // Assuming 'ProjectName' is the property you want to display
                'label' => 'Project Name',
                'attr' => ['class' => 'form-control'],
                'query_builder' => function (ProjectsRepository $er) {
                    // Modify the query here to exclude projects associated with expenses
                    return $er->createQueryBuilder('p')
                        ->leftJoin('p.expenses', 'e')
                        ->where('e.id IS NULL'); // Exclude projects with associated expenses
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Expenses::class,
        ]);
    }
}
