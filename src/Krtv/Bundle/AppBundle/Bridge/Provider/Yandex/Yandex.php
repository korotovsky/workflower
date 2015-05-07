<?php

namespace Krtv\Bundle\AppBundle\Bridge\Provider\Yandex;

use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Stream\Stream;
use Krtv\Bundle\AppBundle\Bridge\Provider\BridgeDeltaInterface;
use Krtv\Bundle\AppBundle\Debounce\DebounceInterface;
use Krtv\Bundle\AppBundle\Entity\Provider\Yandex\YandexAccount;
use Krtv\Bundle\AppBundle\Entity\ProviderAccount;
use Krtv\Bundle\AppBundle\Entity\Transfer\BridgeMessage;
use Krtv\Bundle\AppBundle\Exception\Provider\YandexException;
use Krtv\Bundle\AppBundle\Model\Provider\EntryModelInterface;
use Krtv\Bundle\AppBundle\Model\Provider\Yandex\DeltaModel;
use Krtv\Bundle\AppBundle\Model\Provider\Yandex\Entry\EntryModel;
use Krtv\Bundle\AppBundle\Model\Provider\Yandex\Entry\EntryTrashedModel;
use Psr\Log\LoggerInterface;

/**
 * Class Yandex
 * @package Krtv\Bundle\AppBundle\Bridge\Provider\Yandex
 */
class Yandex implements BridgeDeltaInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Client[]
     */
    private $clients = [];

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
        return 'yandex';
    }

    /**
     * @param ProviderAccount $yandex
     * @param null $cursor
     * @return DeltaModel
     */
    public function getDelta(ProviderAccount $yandex, $cursor = null)
    {
        $items    = [];

        try {
            $this->entityManager->transactional(function () use ($cursor, &$items, $yandex) {
                $qb = $this->entityManager->createQueryBuilder();
                $qb->select('m')
                    ->from(BridgeMessage::class, 'm')
                    ->join('m.provider', 'provider')
                    ->andWhere('m.state = :state')
                        ->setParameter('state', BridgeMessage::STATE_SCHEDULED)
                    ->andWhere('provider = :provider')
                        ->setParameter('provider', $yandex->getId())
                    ->orderBy('m.createdAt', 'ASC');

                if ($cursor !== null) {
                    $qb->andWhere('m.id > :cursor')
                        ->setParameter('cursor', $cursor);
                }

                $items = $qb->getQuery()->getResult();
                foreach ($items as $item /** @var $item BridgeMessage */) {
                    $item->setState(BridgeMessage::STATE_COMPLETED);
                }
            });

            if ($this->logger !== null) {
                $this->logger->info(sprintf('[bridge.yandex] %s items fetched as delta', count($items)));
            }

            $qb = $this->entityManager->createQueryBuilder();
            $qb->select('MAX(m.id)')
                ->from(BridgeMessage::class, 'm')
                ->join('m.provider', 'provider')
                    ->andWhere('m.state = :state')
                ->setParameter('state', BridgeMessage::STATE_COMPLETED)
                    ->andWhere('provider = :provider')
                ->setParameter('provider', $yandex->getId());

            $cursor = $qb->getQuery()->getSingleScalarResult();
        } catch (\Exception $e) {
            throw new YandexException($e->getMessage());
        }

        $entries = [];
        foreach ($items as $bridgeMessage) {
            $changes = $bridgeMessage->getData();

            foreach ($changes['diff']['op'] as $k => $change) {
                $attributes = isset ($change['@attributes'])
                    ? $change['@attributes']
                    : $change;

                if (in_array($attributes['type'], ['published', 'unpublished'])) {
                    continue;
                }

                if ($this->logger !== null) {
                    $this->logger->info(sprintf('[bridge.yandex] attributes: %s', json_encode($attributes)));
                }

                if ($attributes['type'] !== 'deleted') {
                    $entries[] = new EntryModel($attributes['fid'], $attributes);
                } else {
                    $entries[] = new EntryTrashedModel($attributes['fid']);
                }
            }
        }

        if ($this->logger !== null) {
            $this->logger->info(sprintf('[bridge.yandex] %s items after debounce', count($entries)));
        }

        return new DeltaModel($cursor, $items, $entries);
    }

    /**
     * @param ProviderAccount     $account
     * @param EntryModelInterface $entry
     * @return string
     */
    public function getSharedUrl(ProviderAccount $account, EntryModelInterface $entry)
    {
        try {
            $response = $this->createClient($account)->put(sprintf('/v1/disk/resources/publish?path=%s', substr($entry->getPath(), 5)));
            $response = $response->json();

            $response = $this->createClient($account)->get($response['href']);
            $response = $response->json();

            if (empty($response['public_url'])) {
                $message = sprintf('Can\'t create shared link for %s', $entry->getPath());

                if ($this->logger !== null) {
                    $this->logger->critical(sprintf('[bridge.yandex] %s', $message));
                }

                throw new YandexException($message);
            }

            if ($this->logger !== null) {
                $this->logger->info(sprintf('[bridge.yandex] Shared url fetched: %s', $response['public_url']));
            }

            return $response['public_url'];
        } catch (\Exception $e) {
            if ($this->logger !== null) {
                $this->logger->critical(sprintf('[bridge.yandex] Exception: %s', $e->getMessage()));
            }

            throw new YandexException($e->getMessage());
        }
    }

    /**
     * @param ProviderAccount     $account
     * @param EntryModelInterface $entry
     * @return resource
     */
    public function getFile(ProviderAccount $account, EntryModelInterface $entry)
    {
        $stream = Stream::factory(fopen(tempnam(sys_get_temp_dir(), 'yandex'), 'w+b'));

        try {
            $response = $this->createClient($account)->get(sprintf('/v1/disk/resources/download?path=%s', substr($entry->getPath(), 5)));
            $response = $response->json();

            $this->createClient($account)->get($response['href'], ['save_to' => $stream]);

            if ($this->logger !== null) {
                $this->logger->info(sprintf('[bridge.yandex] Blob file fetched: %s', $entry->getPath()));
            }

            return $stream;
        } catch (\Exception $e) {
            if ($this->logger !== null) {
                $this->logger->critical(sprintf('[bridge.yandex] Exception: %s', $e->getMessage()));
            }

            throw new YandexException($e->getMessage());
        }
    }

    /**
     * @param YandexAccount $yandex
     * @return mixed
     */
    private function createClient(YandexAccount $yandex)
    {
        $token = $yandex->getToken();

        if (!isset($this->clients[$token->getAccessToken()])) {
            $this->clients[$token->getAccessToken()] = new Client([
                'base_url' => 'https://cloud-api.yandex.net/',
                'defaults' => [
                    'headers' => [
                        'Authorization' => sprintf('OAuth %s', $token->getAccessToken())
                    ]
                ]
            ]);
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