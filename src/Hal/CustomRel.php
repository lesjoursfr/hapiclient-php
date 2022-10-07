<?php

namespace HapiClient\Hal;

/**
 * The Extension Relation Type described in:
 * - section 8.2 of the HAL specification
 * - section 4 of the RFC5988 - Web Linking document.
 *
 * @see https://datatracker.ietf.org/doc/html/draft-kelly-json-hal-07#section-8.2
 * @see https://tools.ietf.org/html/rfc5988#section-4
 */
final class CustomRel implements CustomRelInterface
{
    private $name;

    /**
     * An Extension Relation Type SHOULD be a URI or a name
     * using the CURIE syntax (prefix:reference).
     * Any name as a string is accepted too.
     *
     * @param string $name the relation name
     */
    public function __construct($name)
    {
        $name = trim($name);
        if (!$name) {
            throw new \InvalidArgumentException("The name can't be empty");
        }

        $this->name = $name;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
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

    // phpcs:ignore Symfony.Commenting.FunctionComment.Missing
    public function __toString()
    {
        return $this->name;
    }
}
