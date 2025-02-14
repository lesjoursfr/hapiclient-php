<?php

namespace HapiClient\Http;

use HapiClient\Hal\ResourceInterface;
use HapiClient\Http\Auth\AuthenticationMethodInterface;

/**
 * The HAPI Client interface.
 */
interface HapiClientInterface
{
    /**
     * @return string the URL pointing to the API server
     */
    public function getApiUrl();

    /**
     * @return string the URL to the entry point Resource
     */
    public function getEntryPointUrl();

    /**
     * @return string the URL pointing to the HAL profile
     */
    public function getProfile();

    /**
     * @return AuthenticationMethodInterface The authentication method
     */
    public function getAuthenticationMethod();

    /**
     * The HAPI Client uses a Guzzle client internally
     * to send all the HTTP requests.
     *
     * @return \GuzzleHttp\ClientInterface The Guzzle client (passed by reference)
     */
    public function &getClient();

    /**
     * @param RequestInterface $request The Request object containing all the parameters necessary for the HTTP request
     *
     * @return ResourceInterface the Resource object returned by the server
     */
    public function sendRequest(RequestInterface $request);

    /**
     * @param array|FollowInterface  $follow   The Follow object or an array of Follow objects containing the parameters necessary for the HTTP request(s)
     * @param ResourceInterface|null $resource The resource containing the link you want to follow. If null, the entry point Resource will be used.
     *
     * @return ResourceInterface the Resource object contained in the last response
     */
    public function sendFollow($follow, ?ResourceInterface $resource = null);

    /**
     * Sends a request to the API entry point URL ("/" by default)
     * and returns its Resource object.
     *
     * The entry point Resource is only retrieved if needed
     * and only once per HapiClient instance.
     *
     * @return ResourceInterface the entry point Resource
     *
     * @throws HttpException
     */
    public function getEntryPointResource();

    /**
     * Attempts to refresh the Resource by sending a GET request
     * to the URL referenced by the "self" relation type.
     * If the resource doesn't have such relation type or the request fails,
     * the same resource is returned.
     *
     * @param ResourceInterface $resource The Resource to refresh
     *
     * @return ResourceInterface The refreshed or the same Resource if failed to refresh it
     */
    public function refresh($resource);
}
