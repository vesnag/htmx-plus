<?php

declare(strict_types=1);

namespace Drupal\htmx_plus_web_1_0_app\Test\Unit\Service;

use Drupal\Core\Database\Connection;
use Drupal\Core\Database\Query\SelectInterface;
use Drupal\Core\Database\StatementInterface;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\htmx_plus_web_1_0_app\Model\ContactData;
use Drupal\htmx_plus_web_1_0_app\Repository\ContactRepository;
use Drupal\htmx_plus_web_1_0_app\Test\Mocks\DatabaseQueryMock;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Drupal\htmx_plus_web_1_0_app\Repository\ContactRepository
 */
class ContactRepositoryTest extends TestCase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection|\PHPUnit\Framework\MockObject\MockObject
   */
  private $database;

  /**
   * The contact repository.
   *
   * @var \Drupal\htmx_plus_web_1_0_app\Repository\ContactRepository
   */
  private $contactRepository;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->database = $this->createMock(Connection::class);
    $this->contactRepository = new ContactRepository($this->database);

    $container = new ContainerBuilder();
    $container->set('database', $this->database);
    \Drupal::setContainer($container);
  }

  /**
   * @covers ::search
   */
  public function testSearch(): void {
    $search = 'test';
    $escapedSearch = 'test';
    $query = $this->createMock(SelectInterface::class);
    $statement = $this->createMock(StatementInterface::class);
    $result = [['id' => 1, 'name' => 'Test Contact', 'email' => 'test@example.com', 'phone' => '1234567890']];

    $this->database->expects($this->once())
      ->method('select')
      ->with('contacts', 'c')
      ->willReturn($query);

    $query->expects($this->once())
      ->method('fields')
      ->with('c')
      ->willReturn($query);

    $this->database->expects($this->once())
      ->method('escapeLike')
      ->with($search)
      ->willReturn($escapedSearch);

    $query->expects($this->once())
      ->method('condition')
      ->with('name', '%' . $escapedSearch . '%', 'LIKE')
      ->willReturn($query);

    $query->expects($this->once())
      ->method('execute')
      ->willReturn($statement);

    $statement->expects($this->once())
      ->method('fetchAll')
      ->willReturn($result);

    $contacts = $this->contactRepository->search($search);

    $this->assertEquals($result, $contacts, 'The search result should match the expected result');
  }

  /**
   * @covers ::saveContact
   */
  public function testSaveContact(): void {
    $contactData = new ContactData(name: 'John Doe', email: 'john.doe@example.com', phone: '1234567890');

    $insertQuery = $this->getMockBuilder(DatabaseQueryMock::class)
      ->onlyMethods(['fields', 'execute'])
      ->getMock();

    $this->database->expects($this->once())
      ->method('insert')
      ->with('contacts')
      ->willReturn($insertQuery);

    $insertQuery->expects($this->once())
      ->method('fields')
      ->with([
        'name' => $contactData->getName(),
        'email' => $contactData->getEmail(),
        'phone' => $contactData->getPhone(),
      ])
      ->willReturnSelf();

    $insertQuery->expects($this->once())
      ->method('execute');

    $this->contactRepository->saveContact($contactData);

    $this->assertTrue(TRUE);
  }

  /**
   * @covers ::all
   */
  public function testAll(): void {
    $query = $this->createMock(SelectInterface::class);
    $statement = $this->createMock(StatementInterface::class);
    $result = [['id' => 1, 'name' => 'Test Contact', 'email' => 'test@example.com', 'phone' => '1234567890']];

    $this->database->expects($this->once())
      ->method('select')
      ->with('contacts', 'c')
      ->willReturn($query);

    $query->expects($this->once())
      ->method('fields')
      ->with('c')
      ->willReturn($query);

    $query->expects($this->once())
      ->method('execute')
      ->willReturn($statement);

    $statement->expects($this->once())
      ->method('fetchAll')
      ->willReturn($result);

    $contacts = $this->contactRepository->all();

    $this->assertEquals($result, $contacts);
  }

  /**
   * @covers ::getContactById
   */
  public function testGetContactById(): void {
    $contact_id = '1';
    $query = $this->createMock(SelectInterface::class);
    $statement = $this->createMock(StatementInterface::class);
    $result = ['id' => 1, 'name' => 'Test Contact', 'email' => 'test@example.com', 'phone' => '1234567890'];

    $this->database->expects($this->once())
      ->method('select')
      ->with('contacts', 'c')
      ->willReturn($query);

    $query->expects($this->once())
      ->method('fields')
      ->with('c', ['id', 'name', 'email', 'phone'])
      ->willReturn($query);

    $query->expects($this->once())
      ->method('condition')
      ->with('id', $contact_id)
      ->willReturn($query);

    $query->expects($this->once())
      ->method('execute')
      ->willReturn($statement);

    $statement->expects($this->once())
      ->method('fetchAssoc')
      ->willReturn($result);

    $contact = $this->contactRepository->getContactById($contact_id);

    $expectedContact = new ContactData(
      $result['name'],
      $result['email'],
      $result['phone'],
      (string) $result['id']
    );

    $this->assertEquals($expectedContact, $contact);

  }

  /**
   * @covers ::updateContact
   */
  public function testUpdateContact(): void {
    $contactData = new ContactData(name: 'John Doe', email: 'john.doe@example.com', phone: '1234567890', id: '1');

    $updateQuery = $this->getMockBuilder(DatabaseQueryMock::class)
      ->onlyMethods(['fields', 'condition', 'execute'])
      ->getMock();

    $this->database->expects($this->once())
      ->method('update')
      ->with('contacts')
      ->willReturn($updateQuery);

    $updateQuery->expects($this->once())
      ->method('fields')
      ->with([
        'name' => $contactData->getName(),
        'email' => $contactData->getEmail(),
        'phone' => $contactData->getPhone(),
      ])
      ->willReturnSelf();

    $updateQuery->expects($this->once())
      ->method('condition')
      ->with('id', $contactData->id())
      ->willReturnSelf();

    $updateQuery->expects($this->once())
      ->method('execute');

    $this->contactRepository->updateContact($contactData);
  }

  /**
   * @covers ::deleteContact
   */
  public function testDeleteContact(): void {
    $contact_id = '1';

    $deleteQuery = $this->getMockBuilder(DatabaseQueryMock::class)
      ->onlyMethods(['condition', 'execute'])
      ->getMock();

    $this->database->expects($this->once())
      ->method('delete')
      ->with('contacts')
      ->willReturn($deleteQuery);

    $deleteQuery->expects($this->once())
      ->method('condition')
      ->with('id', $contact_id)
      ->willReturnSelf();

    $deleteQuery->expects($this->once())
      ->method('execute');

    $this->contactRepository->deleteContact($contact_id);
  }

}
