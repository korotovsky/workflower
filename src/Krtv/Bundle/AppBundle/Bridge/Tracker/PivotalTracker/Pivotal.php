<?php

namespace Krtv\Bundle\AppBundle\Bridge\Tracker\PivotalTracker;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Post\PostFile;
use Krtv\Bundle\AppBundle\Bridge\Tracker\BridgeInterface;
use Krtv\Bundle\AppBundle\Entity\Tracker\PivotalTracker\PivotalTracker;
use Krtv\Bundle\AppBundle\Exception\TrackerException;
use Krtv\Bundle\AppBundle\Model\PivotalTracker\FileModel;
use Krtv\Bundle\AppBundle\Model\PivotalTracker\StoryModel;
use Krtv\Bundle\AppBundle\Model\Provider\EntryModelInterface;

/**
 * Class Pivotal
 * @package Krtv\Bundle\AppBundle\Bridge\Tracker\PivotalTracker
 */
class Pivotal implements BridgeInterface
{
    /**
     * @var Client[]
     */
    private $clients = [];

    /**
     * @return string
     */
    public function getName()
    {
        return 'pivotal';
    }

    /**
     * @param PivotalTracker $pivotal
     * @param EntryModelInterface $entry
     * @param $stream
     * @return FileModel
     */
    public function addFile(PivotalTracker $pivotal, EntryModelInterface $entry, $stream)
    {
        $client = $this->createClient($pivotal->getAccount()->getToken());

        $request = $client->createRequest('POST', sprintf('/services/v5/projects/%s/uploads', $pivotal->getUid()));
        $request->setHeader('Content-Type', 'multipart/form-data');

        $postBody = $request->getBody();
        $postBody->addFile(new PostFile('file', $stream, $entry->getBaseName()));

        try {
            $response = $client->send($request);
        } catch (ClientException $e) {
            throw new TrackerException($e->getResponse()->getBody()->getContents(), $e->getCode(), $e);
        } catch (ServerException $e) {
            throw new TrackerException($e->getResponse()->getBody()->getContents(), $e->getCode(), $e);
        }

        return new FileModel($response->json());
    }

    /**
     * @param PivotalTracker $pivotal
     * @param StoryModel $story
     * @param EntryModelInterface $entry
     * @param FileModel $file
     * @param $text
     */
    public function addComment(PivotalTracker $pivotal, StoryModel $story, EntryModelInterface $entry, FileModel $file = null, $text)
    {
        $client = $this->createClient($pivotal->getAccount()->getToken());

        $json = ['text' => $text];

        if ($file !== null) {
            $json['file_attachments'] = [
                $file->getData()
            ];
        }

        $request = $client->createRequest('POST', sprintf('/services/v5/projects/%s/stories/%s/comments', $story->getProjectId(), $story->getId()), [
            'json' => $json
        ]);
        $request->addHeader('Content-Type', 'application/json');

        try {
            $client->send($request);
        } catch (ClientException $e) {
            throw new TrackerException($e->getResponse()->getBody()->getContents(), $e->getCode(), $e);
        } catch (ServerException $e) {
            throw new TrackerException($e->getResponse()->getBody()->getContents(), $e->getCode(), $e);
        }
    }

    /**
     * @param PivotalTracker $pivotal
     * @param $story
     * @return StoryModel
     */
    public function getStory(PivotalTracker $pivotal, $story)
    {
        $client = $this->createClient($pivotal->getAccount()->getToken());

        $request = $client->createRequest('GET', sprintf('/services/v5/stories/%s', $story));

        try {
            $response = $client->send($request);
        } catch (ClientException $e) {
            throw new TrackerException($e->getResponse()->getBody()->getContents(), $e->getCode(), $e);
        }

        return new StoryModel($response->json());
    }

    /**
     * @param string $accessToken
     * @return Client
     */
    private function createClient($accessToken)
    {
        if (!isset($this->clients[$accessToken])) {
            $this->clients[$accessToken] = new Client([
                'base_url' => 'https://www.pivotaltracker.com',
                'defaults' => [
                    'headers' => [
                        'X-TrackerToken' => $accessToken
                    ]
                ]
            ]);
        }

        return $this->clients[$accessToken];
    }
}