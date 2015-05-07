<?php

namespace Krtv\Bundle\AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Krtv\Bundle\AppBundle\Entity\Provider\Dropbox\DropboxAccount;
use Krtv\Bundle\AppBundle\Entity\Trackers\PivotalTracker\PivotalAccount;
use Krtv\Bundle\AppBundle\Entity\Trackers\PivotalTracker\PivotalProject;

/**
 * Class Transfer
 * @package Krtv\Bundle\AppBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="transfers")
 */
class Transfer
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="Krtv\Bundle\AppBundle\Entity\ProviderAccount")
     * @ORM\JoinTable(name="transfer_provider_refs")
     */
    private $providers;

    /**
     * @ORM\ManyToOne(targetEntity="Krtv\Bundle\AppBundle\Entity\Tracker")
     */
    private $tracker;

    /**
     *
     */
    public function __construct()
    {
        $this->providers = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ProviderAccount[]|ArrayCollection
     */
    public function getProviders()
    {
        return $this->providers;
    }

    /**
     * @param ProviderAccount[]|ArrayCollection $providers
     */
    public function setProviders($providers)
    {
        $this->providers = $providers;
    }

    /**
     * @return Tracker
     */
    public function getTracker()
    {
        return $this->tracker;
    }

    /**
     * @param Tracker $tracker
     */
    public function setTracker(Tracker $tracker)
    {
        $this->tracker = $tracker;
    }
}
