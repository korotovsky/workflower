<?php

namespace Krtv\Bundle\AppBundle\Bridge\Provider\Yandex\Xmpp\EventListener;

use Fabiang\Xmpp\Event\XMLEvent;
use Fabiang\Xmpp\EventListener\AbstractEventListener;

/**
 * Class YandexPing
 * @package Krtv\Bundle\AppBundle\Bridge\Provider\Yandex\Xmpp\EventListener
 */
class Ping extends AbstractEventListener
{
    /**
     * {@inheritDoc}
     */
    public function attachEvents()
    {
        $input = $this->getInputEventManager();
        $input->attach('{urn:xmpp:ping}ping', array($this, 'ping'));
    }

    /**
     * @param XMLEvent $event
     * @return bool
     */
    public function ping(XMLEvent $event)
    {
        if ($event->isEndTag()) {
            /* @var $element \DOMElement */
            $element = $event->getParameter(0);

            $buffer = sprintf('<iq type="result" to="ya.ru" id="%s"/>',
                $element->parentNode->getAttribute('id')
            );

            $this->getConnection()->send($buffer);
        }
    }
}
