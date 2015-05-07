<?php

namespace Krtv\Bundle\AppBundle\Model\Provider\Google\Entry;

use Krtv\Bundle\AppBundle\Matcher\MatcherInterface;
use Krtv\Bundle\AppBundle\Model\Provider\EntryModelInterface;

/**
 * Class EntryModel
 * @package Krtv\Bundle\AppBundle\Model\Provider\Google\Entry
 */
class EntryModel implements MatcherInterface, EntryModelInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var \Google_Service_Drive_DriveFile
     */
    private $data;

    /**
     * @var EntryModel[]
     */
    private $parents = [];

    /**
     * @var EntryModel[]
     */
    private $children = [];

    /**
     * @param $id
     * @param \Google_Service_Drive_DriveFile $data
     */
    public function __construct($id, \Google_Service_Drive_DriveFile $data = null)
    {
        $this->id   = $id;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param EntryModel $entry
     */
    public function addParent(EntryModel $entry)
    {
        if (count($this->parents) > 0) {
            throw new \RuntimeException('Multiple parents does not supported');
        }

        $this->parents[$entry->getId()] = $entry;
    }

    /**
     * @return EntryModel[]
     */
    public function getParents()
    {
        return $this->parents;
    }

    /**
     * @param EntryModel $entry
     */
    public function addChild(EntryModel $entry)
    {
        $this->children[$entry->getId()] = $entry;
    }

    /**
     * @return EntryModel[]
     */
    public function getChildren()
    {
        return $this->parents;
    }

    /**
     * @return string
     */
    public function getBaseName()
    {
        return $this->data->getTitle();
    }

    /**
     * @return string
     */
    public function getPath()
    {
        $parents = $this->getParents();

        if (count($parents) > 0) {
            $parent = current($parents);

            return sprintf('%s/%s', $parent->getPath(), $this->getBaseName());
        } else {
            return sprintf('%s', $this->getBaseName());
        }
    }

    /**
     * @return int
     */
    public function getBytes()
    {
        return (int)$this->data->getFileSize();
    }

    /**
     * @return bool
     */
    public function isDeleted()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isDirectory()
    {
        return $this->data->getMimeType() === 'application/vnd.google-apps.folder';
    }

    /**
     * @return int|null
     */
    public function match()
    {
        if (preg_match('/\/([a-zA-Zа-яА-Я0-9\s-]+)_(\d{8,9})\//', $this->getPath(), $matches)) {
            return $matches[2];
        }

        return null;
    }
}
