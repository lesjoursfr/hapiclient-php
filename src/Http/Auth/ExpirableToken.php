<?php

namespace HapiClient\Http\Auth;

/**
 * A token with an expiration date.
 */
class ExpirableToken
{
    private $value;
    private $expirationTime;

    /**
     * @param string $value          The token value
     * @param int    $expirationTime The token expiration timestamp
     */
    public function __construct(string $value, int $expirationTime)
    {
        $this->value = trim($value);
        $this->expirationTime = $expirationTime;
    }

    /**
     * @return string The token value
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return int The token expiration timestamp
     */
    public function getExpirationTime()
    {
        return $this->expirationTime;
    }

    /**
     * The magic setter is overridden to insure immutability.
     *
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
    }

    /**
     * Checks if the token is still valid until the given time limit.
     *
     * @param int $timeLimit The timestamp representation of the limit
     *
     * @return bool
     */
    public function isValidUntil(int $timeLimit)
    {
        return !empty($this->value) && $this->expirationTime > $timeLimit;
    }
}
