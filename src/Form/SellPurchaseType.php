<?php

namespace App\Form;

use App\Entity\SellPurchase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SellPurchaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('Type', ChoiceType::class, [
                'choices' => [
                    'Sell' => 'Sell',
                    'Purchase' => 'Purchase'
                ]
            ])
            ->add('TransactionDate')
            ->add('SupplierName_ClientName')
            ->add('Porduct_service')
            ->add('Quantity')
            ->add('Coast')
            ->add('Payment_method', ChoiceType::class, [
                'choices' => [
                    'Cheque' => 'cheque',
                    'Cash' => 'Cash',
                    'Bank_transfer' => 'Bank_transfer'
                ]
            ])

            ->add('Note');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SellPurchase::class,
        ]);
    }
}
