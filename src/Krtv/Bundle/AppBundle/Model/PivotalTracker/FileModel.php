<?php

namespace Krtv\Bundle\AppBundle\Model\PivotalTracker;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class FileModel
 * @package Krtv\Bundle\AppBundle\Model\PivotalTracker
 */
class FileModel
{
    /**
     * @var ParameterBag
     */
    private $data;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = new ParameterBag($data);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->data->get('id');
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data->all();
    }
} 