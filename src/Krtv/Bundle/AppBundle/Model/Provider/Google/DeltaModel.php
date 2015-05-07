<?php

namespace Krtv\Bundle\AppBundle\Model\Provider\Google;

use Krtv\Bundle\AppBundle\Model\Provider\DeltaModelInterface;
use Krtv\Bundle\AppBundle\Model\Provider\Google\Entry\EntryModel;

/**
 * Class DeltaModel
 * @package Krtv\Bundle\AppBundle\Model\Provider\Google
 */
class DeltaModel implements DeltaModelInterface
{
    /**
     * @var \Google_Service_Drive_ChangeList
     */
    private $data;

    /**
     * @var EntryModel[]
     */
    private $entries;

    /**
     * @param \Google_Service_Drive_ChangeList $change
     * @param EntryModel[] $entries
     */
    public function __construct(\Google_Service_Drive_ChangeList $change, array $entries = [])
    {
        $this->data = $change;
        $this->entries = $entries;
    }

    /**
     * @return string
     */
    public function getCursor()
    {
        if ($this->hasMore()) {
            return $this->data->getNextPageToken();
        }

        return $this->data->getLargestChangeId();
    }

    /**
     * @return bool
     */
    public function hasMore()
    {
        return $this->data->getNextPageToken() !== null;
    }

    /**
     * @return EntryModelCollection
     */
    public function getEntries()
    {
        return new EntryModelCollection($this->entries);
    }
} 