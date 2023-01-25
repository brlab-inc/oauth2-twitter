# Twitter Provider for OAuth 2.0 Client
[![Latest Version](https://img.shields.io/github/release/thephpleague/oauth2-twitter.svg?style=flat-square)](https://github.com/thephpleague/oauth2-twitter/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/thephpleague/oauth2-twitter/master.svg?style=flat-square)](https://travis-ci.org/thephpleague/oauth2-twitter)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/thephpleague/oauth2-twitter.svg?style=flat-square)](https://scrutinizer-ci.com/g/thephpleague/oauth2-twitter/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/thephpleague/oauth2-twitter.svg?style=flat-square)](https://scrutinizer-ci.com/g/thephpleague/oauth2-twitter)
[![Total Downloads](https://img.shields.io/packagist/dt/league/oauth2-twitter.svg?style=flat-square)](https://packagist.org/packages/league/oauth2-twitter)

This package provides Twitter OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Installation

To install, use composer:

```
composer require brlab-inc/oauth2-twitter
```

## Usage

Usage is the same as The League's OAuth client, using `\BRlab\OAuth2\Client\Provider\Twitter` as the provider.

<!--

    ### Authorization Code Flow

    ```php
    $provider = new League\OAuth2\Client\Provider\Twitter([
        'clientId'          => '{twitter-client-id}',
        'clientSecret'      => '{twitter-client-secret}',
        'redirectUri'       => 'https://example.com/callback-url',
    ]);

    if (!isset($_GET['code'])) {

        // If we don't have an authorization code then get one
        $authUrl = $provider->getAuthorizationUrl();
        $_SESSION['oauth2state'] = $provider->getState();
        header('Location: '.$authUrl);
        exit;

    // Check given state against previously stored one to mitigate CSRF attack
    } elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

        unset($_SESSION['oauth2state']);
        exit('Invalid state');

    } else {

        // Try to get an access token (using the authorization code grant)
        $token = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);

        // Optional: Now you have a token you can look up a users profile data
        try {

            // We got an access token, let's now get the user's details
            $user = $provider->getResourceOwner($token);

            // Use these details to create a new profile
            printf('Hello %s!', $user->getNickname());

        } catch (Exception $e) {

            // Failed to get user details
            exit('Oh dear...');
        }

        // Use this to interact with an API on the users behalf
        echo $token->getToken();
    }
    ```

    ### Managing Scopes

    When creating your Github authorization URL, you can specify the state and scopes your application may authorize.

    ```php
    $options = [
        'state' => 'OPTIONAL_CUSTOM_CONFIGURED_STATE',
        'scope' => ['user','user:email','repo'] // array or string
    ];

    $authorizationUrl = $provider->getAuthorizationUrl($options);
    ```
    If neither are defined, the provider will utilize internal defaults.

    At the time of authoring this documentation, the [following scopes are available](https://developer.github.com/v3/oauth/#scopes).

    - user
    - user:email
    - user:follow
    - public_repo
    - repo
    - repo_deployment
    - repo:status
    - delete_repo
    - notifications
    - gist
    - read:repo_hook
    - write:repo_hook
    - admin:repo_hook
    - admin:org_hook
    - read:org
    - write:org
    - admin:org
    - read:public_key
    - write:public_key
    - admin:public_key

-->

## Testing

``` bash
$ ./vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](https://github.com/thephpleague/oauth2-twitter/blob/master/CONTRIBUTING.md) for details.


## Credits

- [Greg Bowler](https://github.com/g105b)
- [All Contributors](https://github.com/thephpleague/oauth2-twitter/contributors)


## License

The MIT License (MIT). Please see [License File](https://github.com/thephpleague/oauth2-twitter/blob/master/LICENSE) for more information.
