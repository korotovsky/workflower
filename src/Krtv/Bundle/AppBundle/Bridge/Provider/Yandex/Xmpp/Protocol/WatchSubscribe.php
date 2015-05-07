<?php

namespace Krtv\Bundle\AppBundle\Bridge\Provider\Yandex\Xmpp\Protocol;

use Fabiang\Xmpp\Protocol\ProtocolImplementationInterface;
use Fabiang\Xmpp\Util\XML;

/**
 * Class WatchSubscribe
 * @package Krtv\Bundle\AppBundle\Bridge\Provider\Yandex\Xmpp\Protocol
 */
class WatchSubscribe implements ProtocolImplementationInterface
{
    /**
     * Set message receiver.
     *
     * @var string
     */
    protected $to;

    /**
     * @param string $to
     */
    public function __construct($to = '')
    {
        $this->setTo(sprintf('%s@ya.ru', strtolower($to)));
    }

    /**
     * {@inheritDoc}
     */
    public function toString()
    {
        return XML::quoteMessage(
            '<iq to="%s" type="set" id="%s"><s xmlns="yandex:push:disk"/></iq>',
            $this->getTo(),
            XML::generateId()
        );
    }

    /**
     * Get message receiver.
     *
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Set message receiver.
     *
     * @param string $to
     * @return $this
     */
    public function setTo($to)
    {
        $this->to = (string) $to;
        return $this;
    }
}
