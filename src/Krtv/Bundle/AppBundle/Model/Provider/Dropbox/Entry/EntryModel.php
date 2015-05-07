<?php

namespace Krtv\Bundle\AppBundle\Model\Provider\Dropbox\Entry;

use Krtv\Bundle\AppBundle\Matcher\MatcherInterface;
use Krtv\Bundle\AppBundle\Model\Provider\EntryModelInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class EntryModel
 * @package Krtv\Bundle\AppBundle\Model\Provider\Dropbox\Entry
 */
class EntryModel implements MatcherInterface, EntryModelInterface
{
    /**
     * @var
     */
    private $id;

    /**
     * @var ParameterBag
     */
    private $data;

    /**
     * @param $id
     * @param array $data
     */
    public function __construct($id, array $data)
    {
        $this->id   = $id;
        $this->data = new ParameterBag($data);
    }

    /**
     * @return string|int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getBaseName()
    {
        return basename($this->data->get(0));
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
        return $this->data->get(1)['is_dir'] === true;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->data->get(1)['path'];
    }

    /**
     * @return int
     */
    public function getBytes()
    {
        return $this->data->get(1)['bytes'];
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