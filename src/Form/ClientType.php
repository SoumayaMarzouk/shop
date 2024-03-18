<?php

namespace App\Form;

use App\Entity\Client;
use App\Form\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cin',null, ['label' => 'CIN'])
            ->add('nom')
            ->add('tel',null, ['label' => 'Téléphone'])
            ->add('mail')
            ->add('adr',null, ['label' => 'Adresse'])
            ->add('user', UserType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
