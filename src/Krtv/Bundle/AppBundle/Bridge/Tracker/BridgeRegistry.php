<?php

namespace Krtv\Bundle\AppBundle\Bridge\Tracker;

/**
 * Class BridgeRegistry
 * @package Krtv\Bundle\AppBundle\Tracker
 */
class BridgeRegistry
{
    /**
     * @var BridgeInterface[]
     */
    private $bridges = [];

    /**
     * @param BridgeInterface $bridge
     */
    public function addBridge(BridgeInterface $bridge)
    {
        $this->bridges[$bridge->getName()] = $bridge;
    }

    /**
     * @param $name
     * @return BridgeInterface
     */
    public function getBridge($name)
    {
        return $this->bridges[$name];
    }
} 