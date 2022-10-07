<?php

namespace HapiClient\Http\Auth;

use HapiClient\Http;

/**
 * The <a href="https://tools.ietf.org/html/rfc6749">Oauth2 authentication</a> using a
 * <a href="https://tools.ietf.org/html/rfc2617#section-2">Basic authentication</a>
 * to get the access token.
 */
final class Oauth2BasicAuthentication implements AuthenticationMethodInterface
{
    private $tokenEndPointUrl;
    private $userid;
    private $password;
    private $grantType;
    private $scope;
    private $token;

    /**
     * The Oauth2 authentication method using the
     * Basic authorization header composed of a
     * userid and a password.
     *
     * @param string         $tokenEndPointUrl
     * @param string         $userid
     * @param string         $password
     * @param string         $scope            (default to "api")
     * @param string         $grantType        (default to "client_credentials")
     * @param ExpirableToken $token            (optional)
     */
    public function __construct($tokenEndPointUrl, $userid, $password, $scope = 'api', $grantType = 'client_credentials', ExpirableToken $token = null)
    {
        $this->tokenEndPointUrl = $tokenEndPointUrl;
        $this->userid = $userid;
        $this->password = $password;
        $this->grantType = $grantType;
        $this->scope = $scope;
        $this->token = $token;
    }

    /**
     * @return string the API server authentication end point
     */
    public function getTokenEndPointUrl()
    {
        return $this->tokenEndPointUrl;
    }

    /**
     * @return string the first part of the oauth2 authentication
     */
    public function getUserid()
    {
        return $this->userid;
    }

    /**
     * @return string the second part of the oauth2 authentication
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string the grant_type parameter
     */
    public function getGrantType()
    {
        return $this->grantType;
    }

    /**
     * @return string the scope parameter
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @return ExpirableToken the last token used
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * The magic setter is overridden to insure immutability.
     *
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
    }

    /**
     * Adds the authorization header to the request with a valid token.
     * If we do not have a valid token yet, we send a request for one.
     *
     * @param Http\HapiClient $hapiClient The client used to send the request
     * @param Http\Request    $request    The request before it is sent
     *
     * @return Http\Request the same Request with the authorization Headers
     *
     * @throws HttpException
     */
    public function authorizeRequest(Http\HapiClient $hapiClient, Http\Request $request)
    {
        if ($this->isRequestAuthorized($request)) {
            return $request;
        }

        // Request a new access token if needed
        if (!$this->isTokenStillValid()) {
            $this->getAccessToken($hapiClient);
        }

        // Rebuild the request with the new Authorization header
        $headers = $request->getHeaders();
        unset($headers['Authorization']);
        $headers['Authorization'] = 'Bearer '.$this->token->getValue();

        return new Http\Request(
            $request->getUrl(),
            $request->getMethod(),
            $request->getUrlVariables(),
            $request->getMessageBody(),
            $headers
        );
    }

    /**
     * @param Http\Request $request The request before it is sent
     *
     * @return bool false if the request needs to be authorized
     */
    private function isRequestAuthorized(Http\Request $request)
    {
        $headers = $request->getHeaders();
        if (!isset($headers['Authorization'])) {
            return false;
        }

        $authorization = trim($headers['Authorization']);

        return 0 === strpos($authorization, 'Basic') || 0 === strpos($authorization, 'Bearer');
    }

    /**
     * Sends a request for an access token.
     *
     * @param Http\HapiClient $hapiClient The client used to send the request
     *
     * @throws HttpException
     */
    private function getAccessToken(Http\HapiClient $hapiClient)
    {
        $urlEncodedBody = new Http\UrlEncodedBody([
            'grant_type' => $this->grantType,
            'scope' => $this->scope,
        ]);

        $basic = base64_encode($this->userid.':'.$this->password);
        $authorizationHeader = [
            'Accept' => 'application/json',
            'Authorization' => 'Basic '.$basic,
        ];

        $request = new Http\Request(
            $this->tokenEndPointUrl,
            'POST',
            null,
            $urlEncodedBody,
            $authorizationHeader
        );

        // Send the request
        $state = $hapiClient->sendRequest($request)->getState();

        // Check the response
        if (!isset($state['access_token']) || !isset($state['expires_in'])) {
            throw new \Exception('The authentication was a success but the response did not contain the token or its validity limit.');
        }

        // We update the token
        $this->token = new ExpirableToken($state['access_token'], $this->getTime() + $state['expires_in']);
    }

    /**
     * Checks if the token is till valid at the time
     * this method is called.
     *
     * @return bool
     */
    private function isTokenStillValid()
    {
        return null !== $this->token && $this->token->isValidUntil($this->getTime());
    }

    /**
     * It is important to use the same method when setting
     * the expiration time and checking if it is still valid.
     *
     * @return int The current time in seconds
     */
    private function getTime()
    {
        return time();
    }
}
