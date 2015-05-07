<?php

namespace Krtv\Bundle\AppBundle\Bridge\Provider\Yandex\Xmpp\EventListener\Watch;

use Doctrine\ORM\EntityManagerInterface;
use Fabiang\Xmpp\Event\XMLEvent;
use Fabiang\Xmpp\EventListener\AbstractEventListener;
use Fabiang\Xmpp\EventListener\BlockingEventListenerInterface;
use Krtv\Bundle\AppBundle\Entity\ProviderAccount;
use Krtv\Bundle\AppBundle\Entity\Transfer\BridgeMessage;
use Psr\Log\LoggerInterface;

/**
 * Class WatchSubscriber
 * @package Krtv\Bundle\AppBundle\Bridge\Provider\Yandex\Xmpp\EventListener\Watch
 */
class WatchSubscriber extends AbstractEventListener implements BlockingEventListenerInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var ProviderAccount
     */
    protected $provider;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Is event blocking stream.
     *
     * @var boolean
     */
    protected $blocking = false;

    /**
     * @var string
     */
    protected $id;

    /**
     * @param EntityManagerInterface $entityManager
     * @param ProviderAccount        $provider
     */
    public function __construct(EntityManagerInterface $entityManager, ProviderAccount $provider)
    {
        $this->entityManager = $entityManager;
        $this->provider = $provider;
    }

    /**
     * {@inheritDoc}
     */
    public function attachEvents()
    {
        $output = $this->getOutputEventManager();
        $output->attach('{yandex:push:disk}s', array($this, 's'));

        $input = $this->getInputEventManager();
        $input->attach('{jabber:client}iq', array($this, 'iq'));
        $input->attach('{yandex:push:disk}query', array($this, 'query'));
    }

    /**
     * @param XMLEvent $event
     */
    public function s(XMLEvent $event)
    {
        if ($event->isEndTag()) {
            /* @var $element \DOMElement */
            $element = $event->getParameter(0);

            $this->blocking = true;
            $this->id = $element->parentNode->getAttribute('id');
        }
    }

    /**
     * @param XMLEvent $event
     */
    public function iq(XMLEvent $event)
    {
        if ($event->isEndTag()) {
            /* @var $element \DOMElement */
            $element = $event->getParameter(0);

            if ($this->id == $element->getAttribute('id')) {
                $this->blocking = false;
            }
        }
    }

    /**
     * @param XMLEvent $event
     */
    public function query(XMLEvent $event)
    {
        if ($event->isEndTag()) {
            /* @var $element \DOMElement */
            $element = $event->getParameter(0);

            if ($this->logger !== null) {
                $this->logger->info(sprintf('[bridge.yandex_xmpp] Received data from stream'));
            }

            $message = new BridgeMessage();
            $message->setProvider($this->provider);
            $message->setState(BridgeMessage::STATE_SCHEDULED);
            $message->setData($element);

            $this->entityManager->persist($message);
            $this->entityManager->flush();
            $this->entityManager->clear(BridgeMessage::class);
        }
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

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }
}
