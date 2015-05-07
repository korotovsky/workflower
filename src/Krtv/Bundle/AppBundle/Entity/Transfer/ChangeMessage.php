<?php

namespace Krtv\Bundle\AppBundle\Entity\Transfer;

use Doctrine\ORM\Mapping as ORM;
use Krtv\Bundle\AppBundle\Entity\ProviderAccount;
use Krtv\Bundle\AppBundle\Entity\ProviderBridgeAsyncInterface;
use Krtv\Bundle\AppBundle\Entity\ProviderBridgeInterface;
use Krtv\Bundle\AppBundle\Entity\Tracker;
use Krtv\Bundle\AppBundle\Model\Provider\EntryModelInterface;

/**
 * Class TransferChange
 * @package Krtv\Bundle\AppBundle\Entity\Transfer
 *
 * @ORM\Entity()
 * @ORM\Table(name="transfer_changes")
 * @ORM\HasLifecycleCallbacks()
 */
class ChangeMessage
{
    const STATE_NEW         = 0;
    const STATE_SCHEDULED   = 1;
    const STATE_IN_PROGRESS = 2;
    const STATE_COMPLETED   = 3;
    const STATE_ERROR       = -1;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Krtv\Bundle\AppBundle\Entity\ProviderAccount")
     */
    private $provider;

    /**
     * @ORM\ManyToOne(targetEntity="Krtv\Bundle\AppBundle\Entity\Tracker")
     */
    private $tracker;

    /**
     * @ORM\Column(type="text")
     */
    private $data;

    /**
     * @ORM\Column(type="integer")
     */
    private $state;

    /**
     * @var EntryModelInterface
     */
    private $payload;

    /**
     *
     */
    public function __construct()
    {
        $this->state = self::STATE_NEW;
    }

    /**
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param int $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return EntryModelInterface
     */
    public function getData()
    {
        return $this->payload;
    }

    /**
     * @param EntryModelInterface $data
     */
    public function setData(EntryModelInterface $data)
    {
        $this->data = $data;
    }

    /**
     * @return ProviderAccount|ProviderBridgeInterface|ProviderBridgeAsyncInterface
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param ProviderAccount $provider
     */
    public function setProvider(ProviderAccount $provider)
    {
        $this->provider = $provider;
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

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function prePersist()
    {
        $this->data = base64_encode(serialize($this->data));
    }

    /**
     * @ORM\PostLoad()
     */
    public function postLoad()
    {
        $this->data = unserialize(base64_decode($this->data));
        $this->payload = clone $this->data;
    }
}
