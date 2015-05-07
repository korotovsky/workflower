<?php

namespace Krtv\Bundle\AppBundle\Model\PivotalTracker;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class StoryModel
 * @package Krtv\Bundle\AppBundle\Model\PivotalTracker
 */
class StoryModel
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
     * @return int
     */
    public function getProjectId()
    {
        return $this->data->get('project_id');
    }
} 