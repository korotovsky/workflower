<?php

namespace Krtv\Bundle\AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class ProviderAccount
 * @package Krtv\Bundle\AppBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="trackers")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="tracker_type", type="string")
 * @ORM\DiscriminatorMap({
 *    "pivotal" ="Krtv\Bundle\AppBundle\Entity\Tracker\PivotalTracker\PivotalTracker"
 * })
 */
abstract class Tracker
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
    private $uid;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @return string
     */
    abstract public function getBridge();

    /**
     * @return mixed
     */
    abstract public function getAccount();

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
     * @return int
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param int $uid
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s@%s => %s', $this->getName(), $this->getUid(), $this->getAccount()->getName());
    }
} 