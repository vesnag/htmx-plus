<?php

declare(strict_types=1);

namespace Drupal\htmx_plus_web_1_0_app\Test\Unit\Service;

use Drupal\Core\Database\Database;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\htmx_plus_web_1_0_app\Model\Contact;
use Drupal\htmx_plus_web_1_0_app\Repository\ContactRepository;
use PHPUnit\Framework\TestCase;
use Tests\Helper\ContactHelper;

/**
 * @coversDefaultClass \Drupal\htmx_plus_web_1_0_app\Repository\ContactRepository
 */
class ContactRepositoryTest extends TestCase {

  /**
   * The contact helper.
   *
   * @var \Tests\Helper\ContactHelper
   */
  private ContactHelper $contactHelper;

  /**
   * The contact repository.
   *
   * @var \Drupal\htmx_plus_web_1_0_app\Repository\ContactRepository
   */
  private ContactRepository $contactRepository;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $database = Database::getConnection('default', 'default');
    $this->contactHelper = new ContactHelper($database);
    $this->contactRepository = new ContactRepository($database);

    // Prepare the database state.
    $generatedContacts = $this->contactHelper->generateContacts(10);
    $this->contactHelper->insertContactsIntoDatabase($generatedContacts);

    $container = new ContainerBuilder();
    $container->set('database', $database);
    \Drupal::setContainer($container);
  }

  /**
   * {@inheritdoc}
   */
  protected function tearDown(): void {
    $this->contactHelper->truncateContactsTable();
    parent::tearDown();
  }

  /**
   * @covers ::search
   */
  public function testSearch(): void {
    $search = 'Test Contact 1';

    $expectedResults = [
      new Contact(
        name: 'Test Contact 1',
        email: 'test1@example.com',
        phone: '1234567891',
        id: '1'
      ),
      new Contact(
        name: 'Test Contact 10',
        email: 'test10@example.com',
        phone: '12345678910',
        id: '10'
      ),
    ];

    $contacts = $this->contactRepository->search($search);

    $this->assertEquals($expectedResults, $contacts, 'The search result should match the expected result');
  }

  /**
   * @covers ::saveContact
   */
  public function testSaveContact(): void {
    // @todo Fix the test.
    $contact = new Contact(name: 'John Doe', email: 'john.doe@example.com', phone: '1234567890');

    $this->contactRepository->saveContact($contact);

    $this->assertTrue(TRUE);
  }

  /**
   * @covers ::getContacts
   */
  public function testGetAllContacts(): void {
    // @todo Fix the test.
    $expectedContacts = [
      (object) [
        'id' => '1',
        'name' => 'Test Contact',
        'email' => 'test@example.com',
        'phone' => '1234567890',
      ],
    ];

    $actualContacts = $this->contactRepository->getContacts();

    $this->assertEquals($expectedContacts, $actualContacts);
  }

  /**
   * @covers ::getContacts
   */
  public function testGetPaginatedContacts(): void {
    // @todo Fix the test.
    $expectedResult = [['id' => 1, 'name' => 'Test Contact', 'email' => 'test@example.com', 'phone' => '1234567890']];

    $limit = 10;
    $offset = 0;

    $contacts = $this->contactRepository->getContacts($limit, $offset);

    $this->assertEquals($expectedResult, $contacts);
  }

  /**
   * @covers ::getContactById
   */
  public function testGetContactById(): void {
    // @todo Fix the test.
    $contactId = '1';

    $expectedResult = ['id' => 1, 'name' => 'Test Contact', 'email' => 'test@example.com', 'phone' => '1234567890'];

    $contact = $this->contactRepository->getContactById($contactId);

    $expectedContact = new Contact(
      $expectedResult['name'],
      $expectedResult['email'],
      $expectedResult['phone'],
      (string) $expectedResult['id']
    );

    $this->assertEquals($expectedContact, $contact);
  }

  /**
   * @covers ::updateContact
   */
  public function testUpdateContact(): void {
    // @todo Fix the test.
    $contactData = new Contact(name: 'John Doe', email: 'john.doe@example.com', phone: '1234567890', id: '1');

    $this->contactRepository->updateContact($contactData);

    $updatedContact = $this->contactRepository->getContactById('1');

    $expectedContact = new Contact(
      name: 'John Doe',
      email: 'john.doe@example.com',
      phone: '1234567890',
      id: '1'
    );

    $this->assertEquals($expectedContact, $updatedContact);
  }

  /**
   * @covers ::deleteContact
   */
  public function testDeleteContact(): void {
    // @todo Fix the test.
    $contactId = '1';

    $this->contactRepository->deleteContact($contactId);

    $deletedContact = $this->contactRepository->getContactById($contactId);

    $this->assertNull($deletedContact);
  }

}
