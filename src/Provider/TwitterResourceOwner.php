<?php

namespace BRlab\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

/**
 * @property array $response
 * @property string $resourceOwnerId
 */
class TwitterResourceOwner implements ResourceOwnerInterface
{
    /**
     * @var array
     */
    protected $response;

    /**
     * TwitterResourceOwner constructor.
     *
     * @param array $response
     */
    public function __construct(array $response = [])
    {
        $this->response = $response;
    }

    /**
     * Get resource owner id
     *
     * @return string
     */
    public function getId()
    {
        return $this->getResource('id_str');
    }

    /**
     * Get resource owner email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->getResource('email');
    }

    /**
     * Get resource owner name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getResource('name');
    }

    /**
     * Get resource owner nickname
     *
     * @return string
     */
    public function getNickname()
    {
        return $this->getResource('screen_name');
    }

    /**
     * Get resource owner url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->getResource('url');
    }

    protected function getResource($name)
    {
        return isset($this->response[$name]) ? $this->response[$name] : null;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->response;
    }
}
