<?php

namespace Krtv\Bundle\AppBundle\Form\Type\Tracker;

use Krtv\Bundle\AppBundle\Entity\PivotalTracker\PivotalAccount;
use Krtv\Bundle\AppBundle\Entity\Tracker\PivotalTracker\PivotalTrackerAccount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class TrackerAccountType
 * @package Krtv\Bundle\AppBundle\Form\Type\PivotalTracker
 */
class TrackerAccountType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', [
            'label' => 'Account name',
        ]);
        $builder->add('token', 'password', [
            'label' => 'Account token',
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
            'data_class' => PivotalTrackerAccount::class,
        ]);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'tracker_account';
    }
}