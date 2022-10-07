<?php

namespace HapiClient\Hal;

use HapiClient\Exception\EmbeddedResourceNotUniqueException;
use HapiClient\Exception\EmbeddedResourceUniqueException;
use HapiClient\Exception\LinkNotUniqueException;
use HapiClient\Exception\LinkUniqueException;
use HapiClient\Exception\RelNotFoundException;

/**
 * The Resource Object described in the
 * JSON Hypertext Application Language (draft-kelly-json-hal-07).
 *
 * @see https://datatracker.ietf.org/doc/html/draft-kelly-json-hal-07#section-4
 *
 * Note: When trying to find a Link or an embedded Resource
 *       by their relation type (Rel), the search is done by
 *       comparing the lower-case relation name.
 *
 * "When extension relation types are compared, they MUST be compared as
 * strings [...] in a case-insensitive fashion."
 * @see https://tools.ietf.org/html/rfc5988#section-4.2
 */
interface ResourceInterface
{
    /**
     * All the properties of the resource
     * ("_links" and "_embedded" not included).
     *
     * @return array
     */
    public function getState();

    /**
     * All the links directly available in the resource.
     * The key is the relation type (Rel) and the value
     * can be either a Link or a numeric array of Links.
     *
     * Note that there is no guarantees as to the order of the links.
     *
     * @return array
     */
    public function getAllLinks();

    /**
     * All the embedded resources directly available in the resource.
     * The key is the relation type (Rel) and the value
     * can be either a Resource or a numeric array of Resources.
     *
     * Note that there is no guarantees as to the order of the embedded resources.
     *
     * @return array
     */
    public function getAllEmbeddedResources();

    /**
     * Finds a unique link by its relation type.
     *
     * @param RegisteredRel|CustomRel $rel The relation type
     *
     * @return Link the Link referenced by the given rel
     *
     * @throws LinkNotUniqueException
     * @throws RelNotFoundException
     */
    public function getLink($rel);

    /**
     * Finds an array of links by their relation type.
     * Note that there is no guarantees as to the order of the links.
     *
     * @param RegisteredRel|CustomRel $rel The relation type
     *
     * @return array Array of links referenced by the given rel
     *
     * @throws LinkUniqueException
     * @throws RelNotFoundException
     */
    public function getLinks($rel);

    /**
     * Finds a unique embedded resource by its relation type.
     *
     * @param RegisteredRel|CustomRel $rel The relation type
     *
     * @return Resource the Resource referenced by the given rel
     *
     * @throws EmbeddedResourceNotUniqueException
     * @throws RelNotFoundException
     */
    public function getEmbeddedResource($rel);

    /**
     * Finds an array of embedded resources by their relation type.
     * Note that there is no guarantees as to the order of the resources.
     *
     * @param RegisteredRel|CustomRel $rel The relation type
     *
     * @return array Array of embedded resources referenced by the given rel
     *
     * @throws EmbeddedResourceUniqueException
     * @throws RelNotFoundException
     */
    public function getEmbeddedResources($rel);

    /**
     * Builds a Resource from its JSON representation.
     *
     * @param string|array|object $json A JSON representing the resource
     *
     * @return Resource
     */
    public static function fromJson($json);
}
