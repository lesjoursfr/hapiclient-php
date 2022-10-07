<?php

namespace HapiClient\Http;

use HapiClient\Hal\ResourceInterface;

/**
 * The Follow link request interface.
 */
interface FollowInterface
{
    /**
     * Looks for a unique Link referenced by the set
     * relation type (Rel) and returns its href property.
     *
     * @param ResourceInterface $resource The Resource containing a Link referenced by the set relation type (Rel)
     *
     * @return string the URL in the href property of the Link
     *
     * @throws LinkNotUniqueException
     * @throws RelNotFoundException
     */
    public function getUrl(ResourceInterface $resource);

    /**
     * @return Rel the relation type
     */
    public function getRel();

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
