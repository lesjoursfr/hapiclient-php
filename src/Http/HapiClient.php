<?php

namespace HapiClient\Http;

use GuzzleHttp\Client;
use GuzzleHttp\UriTemplate\UriTemplate;
use HapiClient\Exception;
use HapiClient\Hal\RegisteredRel;
use HapiClient\Hal\Resource;
use HapiClient\Hal\ResourceInterface;
use HapiClient\Http\Auth\AuthenticationMethodInterface;
use HapiClient\Util\Misc;

/**
 * An HAPI Client.
 */
final class HapiClient implements HapiClientInterface
{
    private $apiUrl;
    private $entryPointUrl;
    private $profile;
    private $authenticationMethod;

    private $client;

    private $entryPointResource;

    /**
     * @param string                        $apiUrl               The URL pointing to the API server
     * @param string                        $entryPointUrl        The URL to the entry point Resource
     * @param string                        $profile              The URL pointing to the HAL profile containing the resources and their descriptors.
     *                                                            If specified, the client will send an Accept header with application/hal+json and a profile attribute containing the value set here.
     * @param AuthenticationMethodInterface $authenticationMethod The authentication method
     */
    public function __construct($apiUrl = null, $entryPointUrl = '/', $profile = null, AuthenticationMethodInterface $authenticationMethod = null)
    {
        $this->apiUrl = trim($apiUrl);
        $this->entryPointUrl = trim($entryPointUrl);
        $this->profile = trim($profile);
        $this->authenticationMethod = $authenticationMethod;

        if ($this->apiUrl) {
            $baseUrl = rtrim($this->apiUrl, '/').'/';

            $this->client = new Client(['base_uri' => $baseUrl]);
        } else {
            $this->client = new Client();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
    }

    /**
     * {@inheritDoc}
     */
    public function getEntryPointUrl()
    {
        return $this->entryPointUrl;
    }

    /**
     * {@inheritDoc}
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * {@inheritDoc}
     */
    public function getAuthenticationMethod()
    {
        return $this->authenticationMethod;
    }

    /**
     * {@inheritDoc}
     */
    public function &getClient()
    {
        return $this->client;
    }

    /**
     * The magic setter is overridden to insure immutability.
     *
     * @param $name
     * @param $value
     */
    final public function __set($name, $value)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function sendRequest(RequestInterface $request)
    {
        // Options (Guzzle 6+)
        $options = [];
        $options['http_errors'] = false;

        if (($verify = Misc::verify($request->getUrl(), __DIR__.'/../CA/')) !== null) {
            $options['verify'] = $verify;
        }

        // Create the HTTP request
        $httpRequest = $this->createHttpRequest($request);

        // Send the request
        $httpResponse = $this->client->send($httpRequest, $options);

        // If Unauthorized, maybe the authorization just timed out.
        // Try it again to be sure.
        if (401 === $httpResponse->getStatusCode() && null !== $this->authenticationMethod) {
            // Create the HTTP request (and Authorize again)
            $httpRequest = $this->createHttpRequest($request);

            // Execute again
            $httpResponse = $this->client->send($httpRequest, $options);
        }

        // Check the status code (must be 2xx)
        $statusCode = $httpResponse->getStatusCode();
        if ($statusCode >= 200 && $statusCode < 300) {
            return Resource::fromJson((string) $httpResponse->getBody());
        }

        // Exception depending on status code for 3xx, 4xx and 5xx
        if ($statusCode >= 300 && $statusCode < 400) {
            throw new Exception\HttpRedirectionException($httpRequest, $httpResponse);
        }
        if ($statusCode >= 400 && $statusCode < 500) {
            throw new Exception\HttpClientErrorException($httpRequest, $httpResponse);
        }
        if ($statusCode >= 500 && $statusCode < 600) {
            throw new Exception\HttpServerErrorException($httpRequest, $httpResponse);
        }

        throw new Exception\HttpException($httpRequest, $httpResponse);
    }

    /**
     * {@inheritDoc}
     */
    public function sendFollow($follow, ResourceInterface $resource = null)
    {
        if (!$resource) {
            $resource = $this->getEntryPointResource();
        }

        if (!is_array($follow)) {
            $follow = [$follow];
        }

        foreach ($follow as $hop) {
            $resource = $this->sendRequest(new Request(
                $hop->getUrl($resource),
                $hop->getMethod(),
                $hop->getUrlVariables(),
                $hop->getMessageBody(),
                $hop->getHeaders()
            ));
        }

        return $resource;
    }

    /**
     * {@inheritDoc}
     */
    public function getEntryPointResource()
    {
        if ($this->entryPointResource) {
            return $this->entryPointResource;
        }

        return $this->entryPointResource = $this->sendRequest(new Request($this->entryPointUrl));
    }

    /**
     * {@inheritDoc}
     */
    public function refresh($resource)
    {
        try {
            $url = $resource->getLink(RegisteredRel::SELF)->getHref();

            return $this->sendRequest(new Request($url));
        } catch (\Exception $ignored) {
            return $resource;
        }
    }

    /**
     * Instantiates the HttpRequest depending on the
     * configuration from the given Request.
     *
     * @param RequestInterface $request The Request configuration
     *
     * @return \GuzzleHttp\Psr7\Request the HTTP request
     */
    private function createHttpRequest(RequestInterface $request)
    {
        // Handle authentication first
        if ($this->authenticationMethod) {
            $request = $this->authenticationMethod->authorizeRequest($this, $request);
        }

        // The URL
        $url = ltrim(trim($request->getUrl()), '/');

        // Handle templated URLs
        if ($urlVariables = $request->getUrlVariables()) {
            $url = (new UriTemplate())->expand($url, $urlVariables);
        }

        // Headers
        $headers = [];
        $headersToAdd = $request->getHeaders();

        // The message body
        $body = null;
        if ($messageBody = $request->getMessageBody()) {
            $headers['Content-Type'] = $messageBody->getContentType();
            $headers['Content-Length'] = $messageBody->getContentLength();
            $body = $messageBody->getContent();
        }

        // Accept hal+json response
        if ($this->profile) {
            $headers['Accept'] = 'application/hal+json; profile="'.$this->profile.'"';
        } else {
            $headers['Accept'] = 'application/json';
        }

        // Prepare the Guzzle request
        $httpRequest = new \GuzzleHttp\Psr7\Request(
            $request->getMethod(),
            $url,
            array_merge($headers, $headersToAdd),
            $body ? \GuzzleHttp\Psr7\Utils::streamFor($body) : null
        );

        return $httpRequest;
    }
}
