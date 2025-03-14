<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PeopleControllerTest extends WebTestCase
{
    public function testPeopleRoute(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/people');

        $this->assertResponseIsSuccessful();
        //$this->assertSelectorTextContains('h1', 'Hello World');
    }
    public function testAddPeopleRoute()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/people/add');

        $this->assertResponseIsSuccessful();
    }
}
