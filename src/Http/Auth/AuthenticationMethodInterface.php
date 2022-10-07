<?php

namespace HapiClient\Http\Auth;

use HapiClient\Http;

/**
 * The authentication method interface.
 */
interface AuthenticationMethodInterface
{
    /**
     * This is called right before sending the HTTP request.
     *
     * @param Http\HapiClient $hapiClient The client used to send the request
     * @param Http\Request    $request    The request before it is sent
     *
     * @return Request the same Request with the authorization Headers
     *
     * @throws HttpException
     */
    public function authorizeRequest(Http\HapiClient $hapiClient, Http\Request $request);
}
