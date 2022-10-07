<?php

namespace HapiClient\Http;

/**
 * A URL-encoded request message.
 */
class UrlEncodedBody extends AbstractMessageBody
{
    private $query;

    private $content;

    /**
     * @param mixed $query A query string or an associative array representing a query string
     */
    public function __construct($query)
    {
        if (!is_array($query) && !is_string($query)) {
            $queryType = gettype($query);
            throw new \Exception("URL encoded body must be a query string or an associative array representing a query string ('$queryType' provided).");
        }

        $this->query = $query;
    }

    /**
     * @return mixed a query string or an associative array representing a query string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return string the Content-Type header (application/x-www-form-urlencoded)
     */
    public function getContentType()
    {
        return 'application/x-www-form-urlencoded';
    }

    /**
     * @return string the Content-Length header
     */
    public function getContentLength()
    {
        return strlen($this->getContent());
    }

    /**
     * @return string the content
     */
    public function getContent()
    {
        if ($this->content) {
            return $this->content;
        }

        if (is_array($this->query)) {
            return $this->content = http_build_query($this->query);
        }

        return $this->content = $this->query;
    }
}
