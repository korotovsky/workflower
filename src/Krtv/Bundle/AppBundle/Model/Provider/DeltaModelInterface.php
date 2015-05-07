<?php

namespace Krtv\Bundle\AppBundle\Model\Provider;

/**
 * Interface DeltaModelInterface
 * @package Krtv\Bundle\AppBundle\Model\Provider
 */
interface DeltaModelInterface
{
    /**
     * @return string
     */
    public function getCursor();

    /**
     * @return bool
     */
    public function hasMore();

    /**
     * @return EntryModelCollectionInterface
     */
    public function getEntries();
}
