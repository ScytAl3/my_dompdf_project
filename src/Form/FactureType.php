<?php

namespace App\Form;

use App\Entity\Facture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;

class FactureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('client_id', IntegerType::class, [
                'help' => 'Un nombre entier de 4 chiffres',
            ])
            ->add('client_adresse_ip', TextType::class, [
                'help' => 'Une adresse IP normalisée ex: 192.168.0.xxx'
            ])
            ->add('montant_ht', MoneyType::class, [
                'currency' => false
            ])
            ->add('montant_tva', PercentType::class, [
                'scale' => 2,
                'symbol' => false,
                'help' => 'Séparateur décimal à utiliser "."',
            ])
            ->add('facture_createAt', DateType::class, [
                'widget' => 'single_text',
                'html5' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Facture::class,
        ]);
    }
}
