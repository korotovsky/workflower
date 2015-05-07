<?php

namespace Krtv\Bundle\AppBundle\Form\Type\Tracker;

use Krtv\Bundle\AppBundle\Entity\PivotalTracker\PivotalProject;
use Krtv\Bundle\AppBundle\Entity\Tracker\PivotalTracker\PivotalTracker;
use Krtv\Bundle\AppBundle\Entity\Tracker\PivotalTracker\PivotalTrackerAccount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class TrackerType
 * @package Krtv\Bundle\AppBundle\Form\Type\PivotalTracker
 */
class TrackerType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('uid', 'text', [
            'label' => 'Project id',
        ]);
        $builder->add('name', 'text', [
            'label' => 'Project name',
        ]);
        $builder->add('account', 'entity', [
            'label' => 'Choose associated tracker',
            'class' => PivotalTrackerAccount::class,
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
            'data_class' => PivotalTracker::class,
        ]);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'tracker';
    }
}