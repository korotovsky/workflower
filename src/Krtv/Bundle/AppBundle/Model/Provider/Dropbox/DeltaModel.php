<?php

namespace Krtv\Bundle\AppBundle\Model\Provider\Dropbox;

use Krtv\Bundle\AppBundle\Model\Provider\DeltaModelInterface;
use Krtv\Bundle\AppBundle\Model\Provider\Dropbox\Entry\EntryModel;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class DeltaModel
 * @package Krtv\Bundle\AppBundle\Model\Provider\Dropbox
 */
class DeltaModel implements DeltaModelInterface
{
    /**
     * @var ParameterBag
     */
    private $data;

    /**
     * @var EntryModel[]
     */
    private $entries;

    /**
     * @param array $data
     * @param array $entries
     */
    public function __construct(array $data, array $entries = [])
    {
        $this->data    = new ParameterBag($data);
        $this->entries = $entries;
    }

    /**
     * @return string
     */
    public function getCursor()
    {
        return $this->data->get('cursor');
    }

    /**
     * @return bool
     */
    public function hasMore()
    {
        return $this->data->get('has_more') === true;
    }

    /**
     * @return EntryModelCollection
     */
    public function getEntries()
    {
        return new EntryModelCollection($this->entries);
    }
} 