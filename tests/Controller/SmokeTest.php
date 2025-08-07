<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SmokeTest extends WebTestCase
{
    public function testApiDocUrlIsSuccessful(): void
    {
        $client = self::createClient();
        $client->request('GET', 'api/doc');

        self::assertResponseIsSuccessful();
    }

    public function testApiRestaurantUrlIsSecure(): void
    {
        $client = self::createClient();
        $client->request('POST', 'api/restaurant');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoginRouteCanConnectValideUser(): void
    {
        $client = self::createClient();
        $client->followRedirects(false);

        /* se servir des jeux de données pour les appeler et tester les accès , mais dans un prochain cours */

        $client->request('POST', 'api/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
                'username' => 'test@test.com',
                'password' => 'Test1234!',
            ], JSON_THROW_ON_ERROR));

        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertEquals(200, $statusCode);
        $content = $client->getResponse()->getContent();
        $this->assertStringContainsString('user', $content);
    }
}