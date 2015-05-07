<?php

namespace Krtv\Bundle\AppBundle\Model\Provider;

/**
 * Interface EntryModelCollectionInterface
 * @package Krtv\Bundle\AppBundle\Model\Provider
 */
interface EntryModelCollectionInterface
{
    /**
     * @return EntryModelCollectionInterface[]
     */
    public function groupBy();
}
