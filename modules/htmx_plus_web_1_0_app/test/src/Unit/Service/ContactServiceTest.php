<?php

declare(strict_types=1);

namespace Drupal\htmx_plus_web_1_0_app\Test\Unit\Service;

use Drupal\Core\Database\Connection;
use Drupal\Core\Database\Query\SelectInterface;
use Drupal\Core\Database\StatementInterface;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\htmx_plus_web_1_0_app\Service\ContactService;
use Drupal\htmx_plus_web_1_0_app\Test\Mocks\DatabaseQueryMock;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Drupal\htmx_plus_web_1_0_app\Service\ContactService
 */
class ContactServiceTest extends TestCase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $database;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $entityTypeManager;

  /**
   * The contact service.
   *
   * @var \Drupal\htmx_plus_web_1_0_app\Service\ContactService
   */
  protected $contactService;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->database = $this->createMock(Connection::class);
    $entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $this->contactService = new ContactService($this->database, $entityTypeManager);

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

    $contacts = $this->contactService->search($search);

    $this->assertEquals($result, $contacts, 'The search result should match the expected result');
  }

  /**
   * @covers ::saveContact
   */
  public function testSaveContact(): void {
    $name = 'John Doe';
    $email = 'john.doe@example.com';
    $phone = '1234567890';

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
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
      ])
      ->willReturnSelf();

    $insertQuery->expects($this->once())
      ->method('execute');

    $this->contactService->saveContact($name, $email, $phone);

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

    $contacts = $this->contactService->all();

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

    $contact = $this->contactService->getContactById($contact_id);

    $this->assertEquals($result, $contact);
  }

  /**
   * @covers ::updateContact
   */
  public function testUpdateContact(): void {
    $contact_id = '1';
    $name = 'John Doe';
    $email = 'john.doe@example.com';
    $phone = '1234567890';

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
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
      ])
      ->willReturnSelf();

    $updateQuery->expects($this->once())
      ->method('condition')
      ->with('id', $contact_id)
      ->willReturnSelf();

    $updateQuery->expects($this->once())
      ->method('execute');

    $this->contactService->updateContact($contact_id, $name, $email, $phone);
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

    $this->contactService->deleteContact($contact_id);
  }

}
