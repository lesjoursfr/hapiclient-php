<?php

namespace HapiClient\Http;

/**
 * An HTTP Request.
 */
abstract class AbstractRequest
{
    protected $method;
    protected $urlVariables;
    protected $messageBody;
    protected $headers;

    /**
     * @param string                   $method       GET, POST, PUT, PATCH or DELETE
     * @param array|null               $urlVariables The value of the URL variables contained in the URL template
     * @param AbstractMessageBody|null $messageBody  The messageBody to send with the request
     * @param array|null               $headers      Optional headers
     */
    protected function __construct($method = 'GET', ?array $urlVariables = null, ?AbstractMessageBody $messageBody = null, ?array $headers = null)
    {
        $method = strtoupper(trim($method));
        if (!in_array($method, ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'])) {
            throw new \InvalidArgumentException('Method must be one of GET, POST, PUT, PATCH or DELETE.');
        }

        $this->method = $method;
        $this->urlVariables = (array) $urlVariables;
        $this->messageBody = $messageBody;
        $this->headers = (array) $headers;
    }

    /**
     * @return string GET, POST, PUT, PATCH or DELETE
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return array the value of the URL variables contained in the URL template
     */
    public function getUrlVariables()
    {
        return $this->urlVariables;
    }

    /**
     * @return AbstractMessageBody the message body to be sent with the request
     */
    public function getMessageBody()
    {
        return $this->messageBody;
    }

    /**
     * @return array The optional headers
     */
    public function getHeaders()
    {
        return $this->headers;
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
}
