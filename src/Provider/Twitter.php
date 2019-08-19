<?php

namespace BRlab\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Psr\Http\Message\ResponseInterface;
use UnexpectedValueException;
use Abraham\TwitterOAuth\TwitterOAuth;

class Twitter extends AbstractProvider
{
    /**
     * @var \Abraham\TwitterOAuth\TwitterOAuth
     */
    protected $_connection;

    public $oauthToken;
    public $oauthTokenSecret;

    /**
     * Domain
     *
     * @var string
     */
    public $domain = 'https://twitter.com';

    /**
     * Api domain
     *
     * @var string
     */
    public $apiDomain = 'https://api.twitter.com';

    /**
     * Twitter constructor.
     *
     * @param array $options
     * @param array $collaborators
     */
    public function __construct(array $options = [], array $collaborators = [])
    {
        parent::__construct($options, $collaborators);
    }

    /**
     * @return TwitterOAuth
     */
    protected function _initConnection()
    {
        if (!$this->_connection) {
            $this->_connection = new TwitterOAuth($this->clientId, $this->clientSecret);
        }
        return $this->_connection;
    }

    /**
     * @param string $oauthToken
     * @param string $oauthTokenSecret
     * @return $this
     */
    public function setOauthToken($oauthToken, $oauthTokenSecret)
    {
        $this->_initConnection()->setOauthToken($oauthToken, $oauthTokenSecret);
        return $this;
    }

    /**
     * Get authorization headers used by this provider.
     *
     * Typically this is "Bearer" or "MAC". For more information see:
     * http://tools.ietf.org/html/rfc6749#section-7.1
     *
     * No default is provided, providers must overload this method to activate
     * authorization headers.
     *
     * @return array
     */
    protected function getAuthorizationHeaders($token = null)
    {
        return [];
    }

    /**
     * Get authorization url to begin OAuth flow
     *
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        $twitter = $this->_initConnection();
        $uri = $twitter->url('oauth/authenticate', array());
        return $uri;
    }

    public function getAuthorizationParameters(array $options)
    {
        $twitter = $this->_initConnection();
        $requestToken = $twitter->oauth('oauth/request_token', array('oauth_callback' => $this->redirectUri));
        $this->oauthToken = $requestToken['oauth_token'];
        $this->oauthTokenSecret = $requestToken['oauth_token_secret'];
        $options['oauth_token'] = $this->oauthToken;

        return $options;
    }

    /**
     * Get access token url to retrieve token
     *
     * @param array $params
     *
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return TwitterOAuth::API_HOST . '/oauth/access_token';
    }

    public function getAccessToken($grant, array $options = [])
    {
        $grant = $this->verifyGrant($grant);
        $twitter = $this->_initConnection();
        $twitter->setOauthToken($options['oauth_token'], $options['oauth_token_secret']);
        $response = $twitter->oauth('oauth/access_token', ['oauth_verifier' => $options['oauth_verifier']]);
        $prepared = $this->prepareAccessTokenResponse($response);
        $token = $this->createAccessToken($prepared, $grant);

        return $token;
    }

    protected function prepareAccessTokenResponse(array $result)
    {
        $result['access_token'] = $result['oauth_token'];
        $result['resource_owner_id'] = $result['user_id'];
        return parent::prepareAccessTokenResponse($result);
    }

    /**
     * Requests resource owner details.
     *
     * @param AccessToken $token
     * @return mixed
     */
    protected function fetchResourceOwnerDetails(AccessToken $token)
    {
        $values = $token->getValues();
        $twitter = $this->_initConnection();
        $twitter->setOauthToken($token->getToken(), $values['oauth_token_secret']);
        $response = (array)$twitter->get("account/verify_credentials");
        if (false === is_array($response)) {
            throw new UnexpectedValueException(
                'Invalid response received from Authorization Server. Expected JSON.'
            );
        }

        return $response;
    }

    /**
     * Get provider url to fetch user details
     *
     * @param AccessToken $token
     *
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->apiDomain . '/1.1/account/verify_credentials.json';
    }

    /**
     * @param string $method
     * @param string $url
     * @param AccessTokenInterface|string $token
     * @param array $options
     * @return \Psr\Http\Message\RequestInterface
     */
    public function getAuthenticatedRequest($method, $url, $token, array $options = [])
    {
        $options['oauth_token'] = $token->getToken();
        $options['include_entities'] = false;
        $options['skip_status'] = true;
        $options['include_email'] = true;
        return $this->createRequest($method, $url, $token, $options);
    }

    /**
     * Get the default scopes used by this provider.
     *
     * This should not be a complete list of all scopes, but the minimum
     * required for the provider user interface!
     *
     * @return array
     */
    protected function getDefaultScopes()
    {
        return [];
    }

    /**
     * Check a provider response for errors.
     *
     * @param ResponseInterface $response
     * @param string $data Parsed response data
     * @return void
     * @throws IdentityProviderException
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {

    }

    /**
     * Generate a user object from a successful user details request.
     *
     * @param array $response
     * @param AccessToken $token
     * @return TwitterResourceOwner
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        $user = new TwitterResourceOwner($response);
        return $user;
    }
}
