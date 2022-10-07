<?php

namespace HapiClient\Http;

/**
 * An abstract request message.
 */
abstract class AbstractMessageBody
{
    /**
     * @return string the Content-Type header
     */
    abstract public function getContentType();

    /**
     * @return string the Content-Length header
     */
    abstract public function getContentLength();

    /**
     * @return string the content
     */
    abstract public function getContent();

    /**
     * The magic setter is overridden to insure immutability.
     *
     * @param $name
     * @param $value
     */
    final public function __set($name, $value)
    {
    }

    // phpcs:ignore Symfony.Commenting.FunctionComment.Missing
    final public function __toString()
    {
        return $this->getContent();
    }
}
