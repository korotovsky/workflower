<?php

namespace Krtv\Bundle\AppBundle\Bridge\Provider\Yandex\Xmpp\EventListener;

use Fabiang\Xmpp\Event\XMLEvent;
use Fabiang\Xmpp\EventListener\AbstractEventListener;

/**
 * Class Version
 * @package Krtv\Bundle\AppBundle\Bridge\Yandex\Xmpp\EventListener
 */
class Version extends AbstractEventListener
{
    /**
     * {@inheritDoc}
     */
    public function attachEvents()
    {
        $input = $this->getInputEventManager();
        $input->attach('{jabber:iq:version}query', array($this, 'query'));
    }

    /**
     * @param XMLEvent $event
     * @return bool
     */
    public function query(XMLEvent $event)
    {
        if ($event->isEndTag()) {
            $buffer = sprintf('<iq type="result" to="ya.ru" id="ask_version">
                <query xmlns="jabber:iq:version">
                    <name>Workflower</name>
                    <version>1.0</version>
                    <os>Linux</os>
                </query>
            </iq>');

            $this->getConnection()->send($buffer);
        }
    }
}
