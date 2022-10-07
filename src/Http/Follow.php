<?php

namespace HapiClient\Http;

use HapiClient\Hal\ResourceInterface;

/**
 * A Follow link request.
 */
final class Follow extends AbstractRequest implements FollowInterface
{
    private $rel;

    /**
     * @param Rel                 $rel          The relation type
     * @param string              $method       GET, POST, PUT, PATCH or DELETE
     * @param array               $urlVariables The value of the URL variables contained in the URL template
     * @param AbstractMessageBody $messageBody  The messageBody to send with the request
     * @param array               $headers      Optional headers
     */
    public function __construct($rel, $method = 'GET', array $urlVariables = null, AbstractMessageBody $messageBody = null, array $headers = null)
    {
        parent::__construct($method, $urlVariables, $messageBody, $headers);
        $this->rel = $rel;
    }

    /**
     * {@inheritDoc}
     */
    public function getUrl(ResourceInterface $resource)
    {
        return $resource->getLink($this->rel)->getHref();
    }

    /**
     * {@inheritDoc}
     */
    public function getRel()
    {
        return $this->rel;
    }
}
