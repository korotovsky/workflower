<?php

namespace Krtv\Bundle\AppBundle\Bridge\Provider\Google;

use Krtv\Bundle\AppBundle\Bridge\Provider\BridgeDeltaInterface;
use Krtv\Bundle\AppBundle\Bridge\Provider\Google\Drive\TreeFetcher;
use Krtv\Bundle\AppBundle\Debounce\DebounceInterface;
use Krtv\Bundle\AppBundle\Entity\Provider\Google\GoogleAccount;
use Krtv\Bundle\AppBundle\Entity\ProviderAccount;
use Krtv\Bundle\AppBundle\Exception\Provider\GoogleException;
use Krtv\Bundle\AppBundle\Model\Provider\EntryModelInterface;
use Krtv\Bundle\AppBundle\Model\Provider\Google\DeltaModel;
use Psr\Log\LoggerInterface;

/**
 * Class Google
 * @package Krtv\Bundle\AppBundle\Bridge\Provider\Google
 */
class Google implements BridgeDeltaInterface
{
    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $clientSecret;

    /**
     * @var \Google_Client[]
     */
    private $clients = [];

    /**
     * @var TreeFetcher
     */
    private $treeFetcher;

    /**
     * @var DebounceInterface
     */
    private $debounce;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param DebounceInterface $debounce
     * @param string $clientId
     * @param string $clientSecret
     */
    public function __construct(DebounceInterface $debounce, $clientId, $clientSecret)
    {
        $this->clientId     = $clientId;
        $this->clientSecret = $clientSecret;
        $this->debounce     = $debounce;
        $this->treeFetcher  = new TreeFetcher();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'google';
    }

    /**
     * @param ProviderAccount $google
     * @param string|null $cursor
     * @return DeltaModel
     */
    public function getDelta(ProviderAccount $google, $cursor = null)
    {
        $options = [];
        if ($cursor !== null) {
            $options['startChangeId'] = $cursor + 1;
        }

        $driveClient  = $this->createClient($google);
        $driveService = new \Google_Service_Drive($driveClient);

        $response = $driveService->changes->listChanges($options);

        if ($this->logger !== null) {
            $this->logger->info(sprintf('[bridge.google] %s items fetched from API', count($response->getItems())));
        }

        $items = array_filter($response->getItems(), function (\Google_Service_Drive_Change $item) use ($google) {
            if ($item->getFile() === null) {
                return false;
            }
            if ($item->getFile()->getMd5Checksum() === null) {
                return false;
            }

            return $this->debounce->debounce($google, $item->getFileId(), $item->getFile()->getMd5Checksum());
        });

        if ($this->logger !== null) {
            $this->logger->info(sprintf('[bridge.google] %s items after debounce', count($items)));
        }

        $data = $this->treeFetcher->fetch($driveService, $driveClient, $items);

        if ($this->logger !== null) {
            $this->logger->info(sprintf('[bridge.google] %s items are fetched for tree', count($data)));
        }

        return new DeltaModel($response, $data);
    }

    /**
     * @param ProviderAccount $account
     * @param EntryModelInterface $entry
     * @return string
     */
    public function getSharedUrl(ProviderAccount $account, EntryModelInterface $entry)
    {
        $driveClient  = $this->createClient($account);
        $driveService = new \Google_Service_Drive($driveClient);

        $permission = null;

        if ($this->logger !== null) {
            $this->logger->info(sprintf('[bridge.google] Read permissions for %s', $entry->getPath()));
        }

        $response = $driveService->permissions->listPermissions($entry->getId());
        foreach ($response->getItems() as $item /** @var $item \Google_Service_Drive_Permission */) {
            if ($this->logger !== null) {
                $this->logger->info(sprintf('[bridge.google] Permission role=%s, type=%s', $item->getRole(), $item->getType()));
            }

            if ($item->getType() === 'anyone' && $item->getRole() === 'reader') {
                $permission = $item;
            }
        }

        if ($permission === null) {
            if ($this->logger !== null) {
                $this->logger->info(sprintf('[bridge.google] There is not permission with role=reader, type=anyone.'));
            }

            $permission = new \Google_Service_Drive_Permission();
            $permission->setRole('reader');
            $permission->setType('anyone');

            if ($this->logger !== null) {
                $this->logger->info(sprintf('[bridge.google] Insert permission with role=reader, type=anyone for file: %s', $entry->getPath()));
            }

            $response = $driveService->permissions->insert($entry->getId(), $permission);

            if ($this->logger !== null) {
                $this->logger->info(sprintf('[bridge.google] Permission is created: %s', $response->getId()));
            }
        }

        if ($this->logger !== null) {
            $this->logger->info(sprintf('[bridge.google] Fetch metadata for file: %s', $entry->getPath()));
        }

        $response = $driveService->files->get($entry->getId());

        if ($this->logger !== null) {
            $this->logger->info(sprintf('[bridge.google] Metadata is fetched for file: %s. Shared url: %s', $entry->getPath(), $response->getAlternateLink()));
        }

        return $response->getAlternateLink();
    }

    /**
     * @param ProviderAccount $account
     * @param EntryModelInterface $entry
     * @return resource
     */
    public function getFile(ProviderAccount $account, EntryModelInterface $entry)
    {
        $stream = fopen(tempnam(sys_get_temp_dir(), 'google'), 'w+b');

        $driveClient  = $this->createClient($account);
        $driveService = new \Google_Service_Drive($driveClient);

        if ($this->logger !== null) {
            $this->logger->info(sprintf('[bridge.google] Fetch metadata for file: %s', $entry->getPath()));
        }

        $response = $driveService->files->get($entry->getId());

        if ($this->logger !== null) {
            $this->logger->info(sprintf('[bridge.google] Metadata is fetched for file: %s', $entry->getPath()));
        }

        $request = new \Google_Http_Request($response->getDownloadUrl(), 'GET', null, null);

        if ($this->logger !== null) {
            $this->logger->info(sprintf('[bridge.google] Sign download request: %s', $response->getDownloadUrl()));
        }

        $httpRequest = $driveClient->getAuth()->authenticatedRequest($request);

        if ($this->logger !== null) {
            $this->logger->info(sprintf('[bridge.google] Request is signed: %s', $httpRequest->getUrl()));
        }

        if ($httpRequest->getResponseHttpCode() == 200) {
            fwrite($stream, $httpRequest->getResponseBody());

            if ($this->logger !== null) {
                $this->logger->info(sprintf('[bridge.google] Blob file fetched: %s', $entry->getPath()));
            }

            return $stream;
        } else {
            fclose($stream);

            $message = sprintf('Can not download file %s', $entry->getPath());

            if ($this->logger !== null) {
                $this->logger->critical(sprintf('[bridge.google] %s', $message));
            }

            throw new GoogleException($message);
        }
    }

    /**
     * @param GoogleAccount $google
     * @return \Google_Client
     */
    private function createClient(GoogleAccount $google)
    {
        $token = $google->getToken();

        if (!isset($this->clients[$token->getAccessToken()])) {
            $client = new \Google_Client();
            $client->setClientId($this->clientId);
            $client->setClientSecret($this->clientSecret);
            $client->setAccessToken(json_encode([
                'access_token'  => $token->getAccessToken(),
                'refresh_token' => $token->getRefreshToken(),
                'expires_in'    => $token->getLifetime(),
                'created'       => $token->getCreatedAt()->format('U'),
                'token_type'    => 'Bearer',
                'id_token'      => null,
            ]));
            $client->addScope('https://www.googleapis.com/auth/drive.readonly');

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
