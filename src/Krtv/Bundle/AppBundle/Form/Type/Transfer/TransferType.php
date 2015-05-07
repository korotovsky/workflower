<?php

namespace Krtv\Bundle\AppBundle\Form\Type\Transfer;

use Krtv\Bundle\AppBundle\Entity\ProviderAccount;
use Krtv\Bundle\AppBundle\Entity\Tracker;
use Krtv\Bundle\AppBundle\Entity\Transfer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class TransferType
 * @package Krtv\Bundle\AppBundle\Form\Type\Transfer
 */
class TransferType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('providers', 'entity', [
            'label' => 'Choose source providers',
            'class' => ProviderAccount::class,
            'multiple' => true,
            'expanded' => true
        ]);
        $builder->add('tracker', 'entity', [
            'label' => 'Choose associated tracker',
            'class' => Tracker::class,
        ]);
        $builder->add('submit', 'submit', [
            'label' => 'Save',
        ]);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Transfer::class,
        ]);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'transfer';
    }
}