<?php
namespace CognitoApp;

use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;

class CognitoClient
{
    const RESET_REQUIRED    = 'PasswordResetRequiredException';
    const NOT_AUTHHORIZED   = 'NotAuthorizedException';
    const USER_NOT_CONFIRMED= 'UserNotConfirmedException';
    const USER_NOT_FOUND    = 'UserNotFoundException';
    const USERNAME_EXISTS   = 'UsernameExistsException';
    const INVALID_PASSWORD  = 'InvalidPasswordException';
    const CODE_MISMATCH     = 'CodeMismatchException';
    const EXPIRED_CODE      = 'ExpiredCodeException';
    const COOKIE_NAME       = 'access-token';

    private $region;
    private $client_id;
    private $clientSecret;
    private $userpool_id;

    private $client;
    private $user = null;

    public function __construct()
    {
        if(!getenv('REGION') || !getenv('CLIENT_ID') || !getenv('CLIENT_SECRET') || !getenv('USERPOOL_ID')) {
            throw new \InvalidArgumentException("Please provide the region, client_id and userpool_id variables in the .env file");
        }

        $this->region = getenv('REGION');
        $this->client_id = getenv('CLIENT_ID');
        $this->clientSecret = getenv('CLIENT_SECRET');
        $this->userpool_id = getenv('USERPOOL_ID');
    }

    public function initialize() : void
    {
        $this->client = new CognitoIdentityProviderClient([
          'version' => '2016-04-18',
          'region' => $this->region,
        ]);

        try {
            if(isset($_SESSION['access_token'])) {
                $this->user = $this->client->getUser([
                    'AccessToken' => $_SESSION['access_token']
                ]);       
            } else {
                $this->user = $this->client->getUser([
                    'AccessToken' => $this->getAuthenticationCookie()
                ]);
            }
        } catch(\Exception  $e) {

        }
    }

    public function authenticate(string $username, string $password, string $remember) : string
    {
        try {
            $result = $this->client->adminInitiateAuth([
                'AuthFlow' => 'ADMIN_NO_SRP_AUTH',
                'ClientId' => $this->client_id,
                'UserPoolId' => $this->userpool_id,
                'AuthParameters' => [
                    'USERNAME' => $username,
                    'PASSWORD' => $password,
                    'SECRET_HASH' => $this->cognitoSecretHash($username),
                ],
            ]);
        } catch (\Exception $e) {
            // if ($e->getAwsErrorCode() === self::USER_NOT_CONFIRMED) {
            //     return self::USER_NOT_CONFIRMED;
            // } else if ($e->getAwsErrorCode() === self::NOT_AUTHHORIZED) {
            //     return self::NOT_AUTHHORIZED;
            // } else {
                return $e->getMessage();
            // }
        }

        $_SESSION['access_token'] = $result->get('AuthenticationResult')['AccessToken'];
        if(!empty($remember))
            $this->setAuthenticationCookie($result->get('AuthenticationResult')['AccessToken']);

        return '';
    }

    public function signup(string $username, string $email, string $password) : string
    {
        try {
            $result = $this->client->signUp([
                'ClientId'   => $this->client_id,
                'Username'   => $username,
                'Password'   => $password,
                'SecretHash' => $this->cognitoSecretHash($username),
                'UserAttributes' => [
                    [
                        'Name' => 'name',
                        'Value' => $username
                    ],
                    [
                        'Name' => 'email',
                        'Value' => $email
                    ]
                ],
            ]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return '';
    }

    public function confirmSignup(string $username, string $code) : string
    {
        try {
            $result = $this->client->confirmSignUp([
                'ClientId' => $this->client_id,
                'Username' => $username,
                'ConfirmationCode' => $code,
                'SecretHash' => $this->cognitoSecretHash($username),
            ]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return '';
    }

    public function sendPasswordResetMail(string $username) : string
    {
        try {
            $this->client->forgotPassword([
                'ClientId'   => $this->client_id,
                'Username'   => $username,
                'SecretHash' => $this->cognitoSecretHash($username),
            ]);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return '';
    }

    public function resetPassword(string $code, string $password, string $username) : string
    {
        try {
            $this->client->confirmForgotPassword([
                'ClientId' => $this->client_id,
                'ConfirmationCode' => $code,
                'Password' => $password,
                'Username' => $username,
                'SecretHash' => $this->cognitoSecretHash($username),
            ]);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return '';
    }

    public function setUserAttributes($username, array $attributes)
    {
        $this->client->AdminUpdateUserAttributes([
            'Username' => $username,
            'UserPoolId' => $this->userpool_id,
            'UserAttributes' => $this->formatAttributes($attributes),
        ]);

        return true;
    }

    private function formatAttributes(array $attributes)
    {
        $userAttributes = [];

        foreach ($attributes as $key => $value) {
            $userAttributes[] = [
                'Name' => $key,
                'Value' => $value,
            ];
        }

        return $userAttributes;
    }

    public function isAuthenticated() : bool
    {
        return null !== $this->user;
    }

    public function getPoolMetadata() : array
    {
        $result = $this->client->describeUserPool([
            'UserPoolId' => $this->userpool_id,
        ]);

        return $result->get('UserPool');
    }

    public function getPoolUsers() : array
    {
        $result = $this->client->listUsers([
            'UserPoolId' => $this->userpool_id,
        ]);

        return $result->get('Users');
    }

    public function getUser() : ?\Aws\Result
    {
        return $this->user;
    }

    public function logout()
    {
        if(isset($_COOKIE[self::COOKIE_NAME])) {
            unset($_COOKIE[self::COOKIE_NAME]);
            setcookie(self::COOKIE_NAME, '', time() - 3600);
        }
    }

    private function setAuthenticationCookie(string $accessToken) : void
    {
        setcookie(self::COOKIE_NAME, $accessToken, time() + 3600);
    }

    private function getAuthenticationCookie() : string
    {
        return $_COOKIE[self::COOKIE_NAME] ?? '';
    }

    private function hash($message)
    {
        $hash = hash_hmac(
            'sha256',
            $message,
            $this->clientSecret,
            true
        );

        return base64_encode($hash);
    }

    private function cognitoSecretHash($username)
    {
        return $this->hash($username . $this->client_id);
    }
}