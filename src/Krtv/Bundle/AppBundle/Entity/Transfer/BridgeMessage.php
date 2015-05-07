<?php

namespace Krtv\Bundle\AppBundle\Entity\Transfer;

use Doctrine\ORM\Mapping as ORM;
use Krtv\Bundle\AppBundle\Entity\ProviderAccount;

/**
 * Class TransferChange
 * @package Krtv\Bundle\AppBundle\Entity\Transfer
 *
 * @ORM\Entity()
 * @ORM\Table(name="transfer_bridge_messages")
 * @ORM\HasLifecycleCallbacks()
 */
class BridgeMessage
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
     * @ORM\Column(type="array")
     */
    private $data;

    /**
     * @ORM\Column(type="integer")
     */
    private $state;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     *
     */
    public function __construct()
    {
        $this->state     = self::STATE_NEW;
        $this->createdAt = new \DateTime();
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
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param \DOMElement $data
     */
    public function setData(\DOMElement $data)
    {
        $this->data = $this->xmlToArray($data);
    }

    /**
     * @return ProviderAccount
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
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @param $root
     * @return array
     */
    private function xmlToArray($root)
    {
        $result = array();

        if ($root->hasAttributes()) {
            $attributes = $root->attributes;
            foreach ($attributes as $attr) {
                $result['@attributes'][$attr->name] = $attr->value;
            }
        }

        if ($root->hasChildNodes()) {
            $children = $root->childNodes;
            if ($children->length == 1) {
                $child = $children->item(0);
                if ($child->nodeType == XML_TEXT_NODE) {
                    $result['_value'] = $child->nodeValue;
                    return count($result) == 1
                        ? $result['_value']
                        : $result;
                }
            }
            $groups = array();
            foreach ($children as $child) {
                if (!isset($result[$child->nodeName])) {
                    $result[$child->nodeName] = $this->xmlToArray($child);
                } else {
                    if (!isset($groups[$child->nodeName])) {
                        $result[$child->nodeName] = array($result[$child->nodeName]);
                        $groups[$child->nodeName] = 1;
                    }
                    $result[$child->nodeName][] = $this->xmlToArray($child);
                }
            }
        }

        return $result;
    }
}
