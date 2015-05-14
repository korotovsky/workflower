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
 * @ORM\Table(name="transfer_change_bounces")
 * @ORM\HasLifecycleCallbacks()
 */
class BounceMessage
{
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
     * @ORM\Column(type="text")
     */
    private $identifier;

    /**
     * @ORM\Column(type="string")
     */
    private $hash;

    /**
     * @ORM\Column(type="datetime")
     */
    private $expireAt;

    /**
     *
     */
    public function __construct()
    {
        $this->expireAt = new \DateTime('+1 minute 30 seconds');
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
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param mixed $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @return mixed
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param mixed $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }
}
