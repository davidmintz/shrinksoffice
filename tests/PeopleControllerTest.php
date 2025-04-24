<?php

namespace App\Tests;

use App\Entity\Person;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PeopleControllerTest extends WebTestCase
{
    /** @var string[] $dummyData for stuffing Person form */
    protected array $dummyData = [
        'person[firstname]' => 'John',
        'person[middlename]' => 'A.',
        'person[lastname]' => 'Doe',
        'person[alias]' => 'JD',
        'person[email]' => 'john.doe@example.com',
        'person[phone]' => '555-1234',
        'person[address]' => '123 Main St',
        'person[secondary_address]' => 'Apt 4B',
        'person[city]' => 'Springfield',
        'person[state]' => 'MA',
        'person[postal_code]' => '01103',
        'person[payer]' => '', // Assuming no payer is selected
        'person[fee]' => '250',
        'person[active]' => '1', // '1' for active
        'person[type]' => 'patient',
        'person[notes]' => 'blah blah yadda yadda',
    ];

    public function testPeopleRouteWorks(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/people');

        $this->assertResponseIsSuccessful();
        //$this->assertSelectorTextContains('h1', 'Hello World');
    }
    public function testRouteToAddPeopleWorksAndFormIsPresent()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/people/add');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form#person-form',"#person-form not found");
    }

    public function testAddNewPerson(): Person
    {
        $client = static::createClient();

        // Step 1: Request the form page to retrieve the CSRF token
        $crawler = $client->request('GET', '/people/add');

        // Step 2: Select the form and fill in the data
        $form = $crawler->selectButton('Save')->form($this->dummyData);

        // Step 3: Submit the form with the 'X-Requested-With' header
        $client->submit($form, [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        // Step 4: Assert that the response has a 200 status code
        $this->assertResponseStatusCodeSame(200);

        // Step 5: Assert that the response has the 'application/json' content type
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        // Step 6: Decode the JSON response
        $responseData = json_decode($client->getResponse()->getContent(), true);

        // Step 7: Assert that the 'id' key exists in the response
        $this->assertArrayHasKey('id', $responseData);

        // Step 8: Optionally, assert that 'id' is numeric
        $this->assertTrue(is_numeric($responseData['id']));

        // Step 9: Verify that the new Person has been added to the database
        $container = static::getContainer();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get(EntityManagerInterface::class);
        $person = $entityManager->getRepository(Person::class)->findOneBy(['email' => 'john.doe@example.com']);

        $this->assertNotNull($person);
        $this->assertEquals('John', $person->getFirstname());
        $this->assertEquals('A.', $person->getMiddlename());
        $this->assertEquals('Doe', $person->getLastname());
        $this->assertEquals('JD', $person->getAlias());
        $this->assertEquals('555-1234', $person->getPhone());
        $this->assertEquals('123 Main St', $person->getAddress());
        $this->assertEquals('Apt 4B', $person->getSecondaryAddress());
        $this->assertEquals('Springfield', $person->getCity());
        $this->assertEquals('MA', $person->getState());
        $this->assertEquals('01103', $person->getPostalCode());
        $this->assertEquals('250', $person->getFee()/100);
        $this->assertEquals('patient', $person->getType()->value);
        $this->assertEquals('blah blah yadda yadda', $person->getNotes());
        $this->assertTrue($person->isActive());

        return $person;
    }

    /** @depends testAddNewPerson */
    public function testUpdatePerson(Person $person): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/people/update/' . $person->getId(), [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Save')->form();
        $newNotes = 'Updated via test: foo hoo doo daa hoo yeah whatever foo bar baz';
        $form['person[notes]'] = $newNotes;

        $client->submit($form, [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('id', $data);
        $this->assertEquals($person->getId(), $data['id']);

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $updatedPerson = $em->getRepository(Person::class)->find($person->getId());

        $this->assertNotNull($updatedPerson);
        $this->assertSame($newNotes, $updatedPerson->getNotes());
    }

}
