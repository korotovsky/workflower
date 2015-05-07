<?php

namespace Krtv\Bundle\AppBundle\Bridge\Provider\Yandex\Xmpp\EventListener\Authentication;

use Fabiang\Xmpp\Event\XMLEvent;
use Fabiang\Xmpp\EventListener\AbstractEventListener;
use Fabiang\Xmpp\EventListener\BlockingEventListenerInterface;
use Fabiang\Xmpp\EventListener\Stream\Authentication\AuthenticationInterface;
use Fabiang\Xmpp\Util\XML;

/**
 * Handler for "x-yandex-oauth" authentication mechanism.
 *
 * Class YandexOAuth
 * @package Krtv\Bundle\AppBundle\Bridge\Provider\Yandex\Xmpp\EventListener\Authentication
 */
class YandexOAuth extends AbstractEventListener implements AuthenticationInterface, BlockingEventListenerInterface
{
    /**
     * Is event blocking stream.
     *
     * @var boolean
     */
    protected $blocking = false;

    /**
     * {@inheritDoc}
     */
    public function attachEvents()
    {
        $input = $this->getInputEventManager();
        $input->attach('{urn:ietf:params:xml:ns:xmpp-sasl}success', array($this, 'success'));
        $input->attach('{urn:ietf:params:xml:ns:xmpp-sasl}failure', array($this, 'failure'));
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate($username, $token)
    {
        $authString = XML::quote(base64_encode($username . "\x00" . $token));

        $this->blocking = true;

        $auth = sprintf('<auth xmlns="urn:ietf:params:xml:ns:xmpp-sasl" mechanism="X-YANDEX-OAUTH">%s</auth>', $authString);
        $this->getConnection()->send($auth);
    }

    /**
     * @param XMLEvent $event
     */
    public function success(XMLEvent $event)
    {
        $this->blocking = false;
    }

    /**
     * @param XMLEvent $event
     */
    public function failure(XMLEvent $event)
    {
        $this->blocking = false;

        throw new \RuntimeException('OAuth2 authentication failed');
    }

    /**
     * Event listener should return false as long he waits for events to finish.
     *
     * @return boolean
     */
    public function isBlocking()
    {
        return $this->blocking;
    }
}
