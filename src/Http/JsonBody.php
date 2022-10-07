<?php

namespace HapiClient\Http;

/**
 * A JSON request message.
 */
class JsonBody extends AbstractMessageBody
{
    private $json;

    private $content;

    /**
     * @param mixed $json A string, an array or an object representing the JSON body
     */
    public function __construct($json)
    {
        if (!is_array($json) && !is_object($json) && !is_string($json)) {
            $jsonType = gettype($json);
            throw new \Exception("JSON body must be a string, an array or an object representing the JSON body ('$jsonType' provided).");
        }

        $this->json = $json;
    }

    /**
     * @return mixed a string, an array or an object representing the JSON body
     */
    public function getJson()
    {
        return $this->json;
    }

    /**
     * @return string the Content-Type header (application/json)
     */
    public function getContentType()
    {
        return 'application/json';
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

        if (is_array($this->json) || is_object($this->json)) {
            return $this->content = json_encode($this->json, JSON_UNESCAPED_UNICODE);
        }

        return $this->content = $this->json;
    }
}
