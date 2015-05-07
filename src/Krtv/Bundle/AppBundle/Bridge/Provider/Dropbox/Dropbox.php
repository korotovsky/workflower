<?php

namespace Krtv\Bundle\AppBundle\Bridge\Provider\Dropbox;

use Dropbox\Client;
use Krtv\Bundle\AppBundle\Bridge\Provider\BridgeDeltaInterface;
use Krtv\Bundle\AppBundle\Entity\Provider\Dropbox\DropboxAccount;
use Krtv\Bundle\AppBundle\Entity\ProviderAccount;
use Krtv\Bundle\AppBundle\Exception\Provider\DropboxException;
use Krtv\Bundle\AppBundle\Model\Provider\Dropbox\DeltaModel;
use Krtv\Bundle\AppBundle\Model\Provider\Dropbox\Entry\EntryModel;
use Krtv\Bundle\AppBundle\Model\Provider\Dropbox\Entry\EntryTrashedModel;
use Krtv\Bundle\AppBundle\Model\Provider\EntryModelInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Dropbox
 * @package Krtv\Bundle\AppBundle\Bridge\Provider\Dropbox
 */
class Dropbox implements BridgeDeltaInterface
{
    /**
     * @var string
     */
    private $clientId;

    /**
     * @var Client[]
     */
    private $clients = [];

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param $clientId
     */
    public function __construct($clientId)
    {
        $this->clientId = $clientId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'dropbox';
    }

    /**
     * @param ProviderAccount $dropbox
     * @param null $cursor
     * @return DeltaModel
     */
    public function getDelta(ProviderAccount $dropbox, $cursor = null)
    {
        try {
            $response = $this->createClient($dropbox)->getDelta($cursor);
        } catch (\Exception $e) {
            if ($this->logger !== null) {
                $this->logger->critical(sprintf('[bridge.dropbox] %s', $e->getMessage()));
            }

            throw new DropboxException($e->getMessage());
        }

        if ($this->logger !== null) {
            $this->logger->info(sprintf('[bridge.dropbox] %s items fetched as delta', count($response['entries'])));
        }

        $entries = [];
        foreach ($response['entries'] as $entry) {
            if ($entry[1] !== null) {
                $entries[] = new EntryModel($entry[0], $entry);
            } else {
                $entries[] = new EntryTrashedModel($entry[0]);
            }
        }

        return new DeltaModel($response, $entries);
    }

    /**
     * @param ProviderAccount     $dropbox
     * @param EntryModelInterface $entry
     * @return DeltaModel
     */
    public function getSharedUrl(ProviderAccount $dropbox, EntryModelInterface $entry)
    {
        try {
            $url = $this->createClient($dropbox)->createShareableLink($entry->getPath());
            if (empty($url)) {
                $message = sprintf('Can\'t create shared link for %s', $entry->getPath());

                if ($this->logger !== null) {
                    $this->logger->critical(sprintf('[bridge.dropbox] %s', $message));
                }

                throw new DropboxException($message);
            }

            if ($this->logger !== null) {
                $this->logger->info(sprintf('[bridge.dropbox] Shared url fetched: %s', $url));
            }

            return $url;
        } catch (\Exception $e) {
            if ($this->logger !== null) {
                $this->logger->critical(sprintf('[bridge.dropbox] %s', $e->getMessage()));
            }

            throw new DropboxException($e->getMessage());
        }
    }

    /**
     * @param ProviderAccount $dropbox
     * @param EntryModelInterface $entry
     * @return resource
     */
    public function getFile(ProviderAccount $dropbox, EntryModelInterface $entry)
    {
        $stream = fopen(tempnam(sys_get_temp_dir(), 'dropbox'), 'w+b');

        try {
            $metadata = $this->createClient($dropbox)->getFile($entry->getPath(), $stream);
            if ($metadata === null) {
                $message = sprintf('The file %s is not found in dropbox', $entry->getPath());

                if ($this->logger !== null) {
                    $this->logger->critical(sprintf('[bridge.dropbox] %s', $message));
                }

                throw new DropboxException($message);
            }

            if ($this->logger !== null) {
                $this->logger->info(sprintf('[bridge.dropbox] Blob file fetched: %s', $entry->getPath()));
            }
        } catch (\Exception $e) {
            if ($this->logger !== null) {
                $this->logger->critical(sprintf('[bridge.dropbox] Exception: %s', $e->getMessage()));
            }

            throw new DropboxException($e->getMessage());
        }

        return $stream;
    }

    /**
     * @param DropboxAccount $account
     * @return Client
     */
    private function createClient(DropboxAccount $account)
    {
        $token = $account->getToken();

        if (!isset($this->clients[$token->getAccessToken()])) {
            $this->clients[$token->getAccessToken()] = new Client($token->getAccessToken(), $this->clientId);
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