<?php

namespace Krtv\Bundle\AppBundle\Model\Provider\Google;

use Doctrine\Common\Collections\ArrayCollection;
use Krtv\Bundle\AppBundle\Model\Provider\EntryModelCollectionInterface;
use Krtv\Bundle\AppBundle\Model\Provider\Google\Entry\EntryModel;
use Krtv\Bundle\AppBundle\Model\Provider\Google\Entry\EntryTrashedModel;

/**
 * Class EntryModelCollection
 * @package Krtv\Bundle\AppBundle\Collection\Dropbox
 */
class EntryModelCollection extends ArrayCollection implements EntryModelCollectionInterface
{
    /**
     * {@inheritDoc}
     */
    public function __construct(array $elements = array())
    {
        foreach ($elements as $element) {
            if (!($element instanceof EntryModel) && !($element instanceof EntryTrashedModel)) {
                throw new \RuntimeException('Incompatible data type');
            }
        }

        parent::__construct($elements);
    }

    /**
     * {@inheritDoc}
     */
    public function add($value)
    {
        if (!($value instanceof EntryModel) && !($value instanceof EntryTrashedModel)) {
            throw new \RuntimeException('Incompatible data type');
        }

        return parent::add($value);
    }

    /**
     * @return EntryModelCollection[]
     */
    public function groupBy()
    {
        $collections = [];

        foreach ($this as $entry /** @var $entry EntryModel */) {
            $id = $entry->match();

            if ($id === null || $entry instanceof EntryTrashedModel) {
                continue;
            }

            if (!isset($collections[$id])) {
                $collections[$id] = new EntryModelCollection();
            }

            $collections[$id]->add($entry);
        }

        return $collections;
    }
}
