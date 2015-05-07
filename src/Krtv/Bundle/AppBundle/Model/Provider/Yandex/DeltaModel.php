<?php

namespace Krtv\Bundle\AppBundle\Model\Provider\Yandex;

use Krtv\Bundle\AppBundle\Model\Provider\DeltaModelInterface;
use Krtv\Bundle\AppBundle\Model\Provider\Yandex\Entry\EntryModel;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class DeltaModel
 * @package Krtv\Bundle\AppBundle\Model\Provider\Yandex
 */
class DeltaModel implements DeltaModelInterface
{
    /**
     * @var int
     */
    private $cursor;

    /**
     * @var ParameterBag
     */
    private $data;

    /**
     * @var EntryModel[]
     */
    private $entries;

    /**
     * @param $cursor
     * @param array $data
     * @param array $entries
     */
    public function __construct($cursor, array $data, array $entries = [])
    {
        $this->cursor  = $cursor;
        $this->data    = new ParameterBag($data);
        $this->entries = $entries;
    }

    /**
     * @return string
     */
    public function getCursor()
    {
        return $this->cursor;
    }

    /**
     * @return bool
     */
    public function hasMore()
    {
        return count($this->data) > 0;
    }

    /**
     * @return EntryModelCollection
     */
    public function getEntries()
    {
        return new EntryModelCollection($this->entries);
    }
} 