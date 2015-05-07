<?php

namespace Krtv\Bundle\AppBundle\Bridge\Provider\Yandex;

use Doctrine\ORM\EntityManagerInterface;
use Fabiang\Xmpp\Client;
use Fabiang\Xmpp\Options;
use Krtv\Bundle\AppBundle\Bridge\Provider\BridgeAsyncInterface;
use Krtv\Bundle\AppBundle\Bridge\Provider\Yandex\Xmpp\EventListener\Authentication\YandexOAuth;
use Krtv\Bundle\AppBundle\Bridge\Provider\Yandex\Xmpp\EventListener\Ping;
use Krtv\Bundle\AppBundle\Bridge\Provider\Yandex\Xmpp\EventListener\Version;
use Krtv\Bundle\AppBundle\Bridge\Provider\Yandex\Xmpp\EventListener\Watch\WatchSubscriber;
use Krtv\Bundle\AppBundle\Bridge\Provider\Yandex\Xmpp\Protocol\WatchSubscribe;
use Krtv\Bundle\AppBundle\Entity\Provider\Yandex\YandexAccount;
use Krtv\Bundle\AppBundle\Entity\ProviderAccount;
use Psr\Log\LoggerInterface;

/**
 * Class YandexAsync
 * @package Krtv\Bundle\AppBundle\Bridge\Provider\Yandex
 */
class YandexAsync implements BridgeAsyncInterface
{
    /**
     * @var Client[]
     */
    private $clients = [];

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'yandex_xmpp';
    }

    /**
     * @param ProviderAccount $account
     * @return mixed|void
     */
    public function execute(ProviderAccount $account)
    {
        $client = $this->createClient($account);

        try {
            if (!$client->getConnection()->isConnected()) {
                if ($this->logger !== null) {
                    $this->logger->info(sprintf('[bridge.yandex_xmpp] Connecting to server.'));
                }

                $client->connect();
                $client->send(new WatchSubscribe($account->getName()));
                $client->getConnection()->getSocket()->setBlocking(false);
            }

            $client->getConnection()->receive();
        } catch (\Exception $e) {
            $client->disconnect();

            if ($this->logger !== null) {
                $this->logger->info(sprintf('[bridge.yandex_xmpp] Exception: %s. Disconnected from server.', $e->getMessage()));
            }
        }
    }

    /**
     * @param YandexAccount $account
     * @return Client
     */
    private function createClient(YandexAccount $account)
    {
        $token = $account->getToken();

        if (!isset($this->clients[$token->getAccessToken()])) {
            $options = new Options('tcp://push.xmpp.yandex.ru:5222');
            $options->setTo('ya.ru');
            $options->setTimeout(31104000);
            $options->setUsername($account->getName());
            $options->setPassword($token->getAccessToken());
            $options->setAuthenticationClasses(
                $options->getAuthenticationClasses() + ['x-yandex-oauth' => YandexOAuth::class]
            );

            $client = new Client($options);

            $subscriber = new WatchSubscriber($this->entityManager, $account);
            $subscriber->setLogger($this->logger);

            $options->getImplementation()->registerListener($subscriber);
            $options->getImplementation()->registerListener(new Version());
            $options->getImplementation()->registerListener(new Ping());

            $this->clients[$token->getAccessToken()] = $client;
        }

        return $this->clients[$token->getAccessToken()];
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }
} 