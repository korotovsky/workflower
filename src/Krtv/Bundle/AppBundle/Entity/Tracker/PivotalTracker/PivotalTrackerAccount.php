<?php

namespace Krtv\Bundle\AppBundle\Entity\Tracker\PivotalTracker;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class PivotalTrackerAccount
 * @package Krtv\Bundle\AppBundle\Entity\Tracker\PivotalTracker
 *
 * @ORM\Entity()
 * @ORM\Table(name="tracker_type_pivotal_accounts")
 */
class PivotalTrackerAccount
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     */
    private $token;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s', $this->getName());
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }
} 