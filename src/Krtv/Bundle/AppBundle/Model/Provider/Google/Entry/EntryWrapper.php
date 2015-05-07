<?php

namespace Krtv\Bundle\AppBundle\Model\Provider\Google\Entry;

use Krtv\Bundle\AppBundle\Model\Provider\EntryModelInterface;

/**
 * Class EntryWrapper
 * @package Krtv\Bundle\AppBundle\Model\Provider\Google\Entry
 */
class EntryWrapper
{
    /**
     * @var EntryModelInterface
     */
    private $data;

    /**
     * @var \Google_Service_Drive_ParentReference[]
     */
    private $parents = [];

    /**
     * @param EntryModelInterface $data
     * @param array               $parents
     */
    public function __construct(EntryModelInterface $data, array $parents = [])
    {
        $this->data = $data;
        $this->parents = $parents;
    }

    /**
     * @return EntryModel
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return EntryModel[]
     */
    public function getParents()
    {
        return $this->parents;
    }
}