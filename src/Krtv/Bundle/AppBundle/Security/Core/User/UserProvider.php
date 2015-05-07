<?php

namespace Krtv\Bundle\AppBundle\Security\Core\User;

use Doctrine\ORM\EntityManagerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUser;
use Krtv\Bundle\AppBundle\Entity\ProviderAccount;
use Krtv\Bundle\AppBundle\Entity\ProviderAccountToken;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class UserProvider
 * @package Krtv\Bundle\AppBundle\Security\Core\User
 */
class UserProvider implements UserProviderInterface, OAuthAwareUserProviderInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritDoc}
     */
    public function loadUserByUsername($username)
    {
        return new OAuthUser($username);
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $classMetadata = $this->entityManager->getClassMetadata(ProviderAccount::class);
        $class = $classMetadata->discriminatorMap[$response->getResourceOwner()->getName()];

        $provider = $this->entityManager->getRepository($class)->findOneBy([
            'uid' => $response->getUsername(),
        ]);

        if ($provider === null) {
            $provider = new $class(); /** @var $provider ProviderAccount */

            $token = new ProviderAccountToken();
            $token->setAccessToken($response->getAccessToken());
            $token->setRefreshToken($response->getRefreshToken());
            $token->setCreatedAt(new \DateTime());
            $token->setAccount($provider);

            if ($response->getExpiresIn() !== null) {
                $token->setLifetime($response->getExpiresIn());
            }

            $name = isset($response->getResponse()['login']) !== false
                ? $response->getResponse()['login']
                : $response->getRealName();

            $provider->setName($name);
            $provider->setUid($response->getUsername());
            $provider->addToken($token);

            $this->entityManager->persist($token);
            $this->entityManager->persist($provider);
            $this->entityManager->flush();
        }

        return $this->loadUserByUsername($response->getUsername());
    }

    /**
     * {@inheritDoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException(sprintf('Unsupported user class "%s"', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return $class === OAuthUser::class;
    }
}