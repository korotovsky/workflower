<?php

namespace Krtv\Bundle\AppBundle\Model\Provider;

/**
 * Interface EntryModelInterface
 * @package Krtv\Bundle\AppBundle\Model\Provider
 */
interface EntryModelInterface
{
    /**
     * @return string|int
     */
    public function getId();

    /**
     * @return string
     */
    public function getBaseName();

    /**
     * @return string
     */
    public function getPath();

    /**
     * @return int
     */
    public function getBytes();

    /**
     * @return bool
     */
    public function isDeleted();

    /**
     * @return bool
     */
    public function isDirectory();
}
