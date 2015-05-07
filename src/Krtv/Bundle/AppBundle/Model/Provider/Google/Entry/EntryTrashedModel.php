<?php

namespace Krtv\Bundle\AppBundle\Model\Provider\Google\Entry;

use Krtv\Bundle\AppBundle\Matcher\MatcherInterface;
use Krtv\Bundle\AppBundle\Model\Provider\EntryModelInterface;

/**
 * Class EntryTrashedModel
 * @package Krtv\Bundle\AppBundle\Model\Provider\Google\Entry
 */
class EntryTrashedModel implements MatcherInterface, EntryModelInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @param $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return null
     */
    public function getPath()
    {
        return null;
    }

    /**
     * @return null
     */
    public function getBaseName()
    {
        return null;
    }

    /**
     * @return null
     */
    public function getBytes()
    {
        return null;
    }

    /**
     * @return null
     */
    public function isDeleted()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isDirectory()
    {
        return false;
    }

    /**
     * @return null
     */
    public function match()
    {
        return null;
    }
}
