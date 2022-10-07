<?php

namespace HapiClient\Exception;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use HapiClient\Hal\Resource;

/**
 * Raised when the server responds with an HTTP error code.
 */
class HttpException extends \Exception
{
    private $request;
    private $response;

    /**
     * Create an HttpException.
     *
     * @param Request  $request  The HTTP request
     * @param Response $response The HTTP response
     */
    public function __construct(Request $request, Response $response)
    {
        parent::__construct($response->getStatusCode().' '.$response->getReasonPhrase());

        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @return Request the HTTP request causing the Exception
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Response the HTTP response causing the Exception
     */
    public function getResponse()
    {
        return $this->response;
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
     * This is basically a shortcut for for getResponse()->getStatusCode().
     *
     * @return string the HTTP status code
     */
    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }

    /**
     * This is basically a shortcut for getResponse()->getReasonPhrase().
     *
     * @return string the HTTP reason phrase
     */
    public function getReasonPhrase()
    {
        return $this->response->getReasonPhrase();
    }

    /**
     * This is basically a shortcut for (string) getResponse()->getBody().
     *
     * @return string the response body
     */
    public function getResponseBody()
    {
        return (string) $this->response->getBody();
    }

    /**
     * The response message body may be a string
     * representation of a Resource representing the error.
     *
     * This is basically a shortcut for Resource::fromJson(getResponseBody()).
     *
     * @return Resource the Resource returned by the response (may be empty)
     */
    public function getResponseResource()
    {
        return Resource::fromJson($this->getResponseBody());
    }
}
