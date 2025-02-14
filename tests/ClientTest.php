<?php

// phpcs:disable Symfony.Commenting,PSR1.Classes.ClassDeclaration.MissingNamespace

namespace HapiClient\Tests;

use HapiClient\Exception;
use HapiClient\Hal;
use HapiClient\Http;
use HapiClient\Http\Auth;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public const APIURL = 'https://api.preprod.slimpay.com';
    public const PROFILEURL = 'https://api.slimpay.net/alps/v1';
    public const APPID = 'democreditor01';
    public const APPSECRET = 'demosecret01';
    public const CREDITOR_REFERENCE = 'democreditor';
    public const SCOPE = 'api';
    public const REL_NS = 'https://api.slimpay.net/alps#';

    private $hapiClient;

    protected function setUp(): void
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

    public function testOneFollowWithGet()
    {
        // Follow the get-creditors link
        $rel = new Hal\CustomRel(self::REL_NS.'get-creditors');
        $follow = new Http\Follow($rel, 'GET', ['reference' => self::CREDITOR_REFERENCE]);
        $creditor = $this->hapiClient->sendFollow($follow);

        $this->assertEquals(self::CREDITOR_REFERENCE, $creditor->getState()['reference']);
    }

    public function testTwoFollowsWithGet()
    {
        // Follow the get-creditors then get-mandates links
        $rel = new Hal\CustomRel(self::REL_NS.'get-creditors');
        $follow1 = new Http\Follow($rel, 'GET', ['reference' => self::CREDITOR_REFERENCE]);

        $rel = new Hal\CustomRel(self::REL_NS.'get-mandates');
        $follow2 = new Http\Follow($rel, 'GET', ['reference' => 'SLMP040462818']);

        $mandate = $this->hapiClient->sendFollow([$follow1, $follow2]);

        $this->assertEquals('SLMP040462818', $mandate->getState()['reference']);
    }

    public function testOneWrongFollowWithGet()
    {
        // Follow the get-creditors link
        $rel = new Hal\CustomRel(self::REL_NS.'get-creditors');
        $follow = new Http\Follow($rel, 'GET', ['reference' => 'noaccesstothiscreditor']);

        try {
            $this->hapiClient->sendFollow($follow);
        } catch (Exception\HttpClientErrorException $e) {
            $this->assertEquals(403, $e->getStatusCode());
            $this->assertEquals('Forbidden', $e->getReasonPhrase());
        }
    }

    public function testRefreshResource()
    {
        // Follow the get-creditors link
        $rel = new Hal\CustomRel(self::REL_NS.'get-creditors');
        $follow = new Http\Follow($rel, 'GET', ['reference' => self::CREDITOR_REFERENCE]);
        $creditor = $this->hapiClient->sendFollow($follow);

        // Create the same Resource but with no state
        // to simulate a "partial embedded resource".
        $creditorToRefresh = new Hal\Resource(
            [],
            $creditor->getAllLinks(),
            $creditor->getAllEmbeddedResources()
        );
        $this->assertNotEquals($creditor, $creditorToRefresh);

        // Try to "refresh" it.
        $refreshedCreditor = $this->hapiClient->refresh($creditorToRefresh);

        // Compare the original creditor with the refreshed one
        $this->assertEquals($creditor, $refreshedCreditor);
    }
}
