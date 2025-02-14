<?php

namespace HapiClient\Http;

/**
 * A classic Request.
 */
final class Request extends AbstractRequest implements RequestInterface
{
    private $url;

    /**
     * @param string                   $url          The URL
     * @param string                   $method       GET (default), POST, PUT, PATCH or DELETE
     * @param array|null               $urlVariables The value of the URL variables contained in the URL template
     * @param AbstractMessageBody|null $messageBody  The messageBody to send with the request
     * @param array|null               $headers      Optional headers
     */
    public function __construct($url, $method = 'GET', ?array $urlVariables = null, ?AbstractMessageBody $messageBody = null, ?array $headers = null)
    {
        parent::__construct($method, $urlVariables, $messageBody, $headers);

        // Validate the URL
        $url = trim($url);
        if (!$url) {
            throw new \InvalidArgumentException('URL is empty.');
        }

        $this->url = $url;
    }

    /**
     * @return string The URL
     */
    public function getUrl()
    {
        return $this->url;
    }
}
