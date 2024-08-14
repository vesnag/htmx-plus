<?php

declare(strict_types=1);

namespace Drupal\htmx_plus_web_1_0_app\Test\Kernel;

use Drupal\htmx_plus_web_1_0_app\Model\Contact;

use Drupal\htmx_plus_web_1_0_app\Repository\ContactRepository;
use Drupal\KernelTests\KernelTestBase;
use Tests\Helper\ContactHelper;

/**
 * @coversDefaultClass \Drupal\htmx_plus_web_1_0_app\Repository\ContactRepository
 */
class ContactRepositoryTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'htmx_plus',
    'htmx_plus_web_1_0_app',
  ];

  /**
   * The contact repository.
   *
   * @var \Drupal\htmx_plus_web_1_0_app\Repository\ContactRepository
   */
  private ContactRepository $contactRepository;

  /**
   * The contact helper.
   *
   * @var \Tests\Helper\ContactHelper
   */
  private ContactHelper $contactHelper;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installSchema('htmx_plus_web_1_0_app', 'contacts');

    $this->contactRepository = $this->container->get('htmx_plus_web_1_0_app.contact_repository');
    $this->contactHelper = new ContactHelper($this->container->get('database'));
  }

  /**
   * {@inheritdoc}
   */
  protected function tearDown(): void {
    $this->contactHelper->truncateContactsTable();
    parent::tearDown();
  }

  /**
   * @covers ::saveContact
   * @covers ::getContactById
   */
  public function testSaveAndRetrieveContact(): void {
    $contact = new Contact('John Doe', 'john.doe@example.com', '1234567890');
    $this->contactRepository->saveContact($contact);

    $savedContact = $this->contactRepository->getContactById('1');
    $this->assertNotNull($savedContact);
    $this->assertEquals('John Doe', $savedContact->getName());
    $this->assertEquals('john.doe@example.com', $savedContact->getEmail());
    $this->assertEquals('1234567890', $savedContact->getPhone());
  }

  /**
   * @covers ::search
   */
  public function testSearchContact(): void {
    $this->contactHelper->generateAndInsertContacts(10);

    $search_string = 'Test Contact 1';
    $searchResults = $this->contactRepository->search($search_string);

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

    $this->assertEquals($expectedResults, $searchResults, 'The search result should match the expected result');
  }

  /**
   * @covers ::getContacts
   */
  public function testGetContactsWithPagination(): void {
    $this->contactHelper->generateAndInsertContacts(20);

    $this->assertContactsPagination(10, 0, 10, 'Test Contact 1');
    $this->assertContactsPagination(5, 10, 5, 'Test Contact 11');
  }

  /**
   * @covers ::updateContact
   * @covers ::getContactById
   */
  public function testUpdateContact(): void {
    $this->contactHelper->generateAndInsertContacts(1);

    $savedContact = $this->contactRepository->getContactById('1');
    $this->assertNotNull($savedContact);

    $savedContact->setName('John Updated');
    $this->contactRepository->updateContact($savedContact);

    $updatedContact = $this->contactRepository->getContactById('1');
    $this->assertNotNull($updatedContact, 'Contact should not be null');
    $this->assertEquals('John Updated', $updatedContact->getName());
  }

  /**
   * @covers ::deleteContact
   * @covers ::getContactById
   */
  public function testDeleteContact(): void {
    $this->contactHelper->generateAndInsertContacts(1);

    $this->contactRepository->deleteContact('1');
    $deletedContact = $this->contactRepository->getContactById('1');
    $this->assertNull($deletedContact);
  }

  /**
   * @covers ::doesEmailExist
   */
  public function testDoesEmailExist(): void {
    $this->contactHelper->generateAndInsertContacts(1);

    $this->assertTrue($this->contactRepository->doesEmailExist('test1@example.com'));
    $this->assertFalse($this->contactRepository->doesEmailExist('non.existent@example.com'));
  }

  /**
   * Asserts the contacts pagination.
   *
   * @param int $limit
   *   The limit for the pagination.
   * @param int $offset
   *   The offset for the pagination.
   * @param int $expectedCount
   *   The expected number of contacts.
   * @param string $expectedFirstContactName
   *   The expected name of the first contact.
   */
  private function assertContactsPagination(int $limit, int $offset, int $expectedCount, string $expectedFirstContactName): void {
    $contacts = $this->contactRepository->getContacts($limit, $offset);
    $this->assertCount($expectedCount, $contacts);
    $this->assertEquals($expectedFirstContactName, $contacts[0]->getName());
  }

}
