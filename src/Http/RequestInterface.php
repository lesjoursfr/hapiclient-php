<?php

namespace HapiClient\Http;

/**
 * The classic Request interface.
 */
interface RequestInterface
{
    /**
     * @return string The URL
     */
    public function getUrl();

    /**
     * @return string GET, POST, PUT, PATCH or DELETE
     */
    public function getMethod();

    /**
     * @return array the value of the URL variables contained in the URL template
     */
    public function getUrlVariables();

    /**
     * @return AbstractMessageBody the message body to be sent with the request
     */
    public function getMessageBody();

    /**
     * @return array The optional headers
     */
    public function getHeaders();
}
