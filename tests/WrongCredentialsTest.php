<?php

namespace HapiClient\tests;

use HapiClient\Exception;
use HapiClient\Http;
use HapiClient\Http\Auth;
use PHPUnit\Framework\TestCase;

// phpcs:ignore Symfony.Commenting.ClassComment.Missing
class WrongCredentialsTest extends TestCase
{
    public const APIURL = 'https://api.preprod.slimpay.com';
    public const PROFILEURL = 'https://api.slimpay.net/alps/v1';
    public const APPID = 'democreditor01';
    public const APPSECRET = 'wrongsecret';
    public const SCOPE = 'api';

    private $hapiClient;

    /**
     * @before
     */
    public function initClient()
    {
        $this->hapiClient = new Http\HapiClient(
            self::APIURL,
            '/',
            self::PROFILEURL,
            new Auth\Oauth2BasicAuthentication(
                '/oauth/token',
                self::APPID,
                self::APPSECRET,
                self::SCOPE
            )
        );
    }

    /**
     * @test
     */
    public function test()
    {
        try {
            $this->hapiClient->getEntryPointResource();
            throw new \Exception('HttpClientErrorException was not raised while using wrong credentials.');
        } catch (Exception\HttpClientErrorException $e) {
            $this->assertEquals(401, $e->getStatusCode());
            $this->assertEquals('Unauthorized', $e->getReasonPhrase());
        }
    }
}
