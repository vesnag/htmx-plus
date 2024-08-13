<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Drupal\htmx_plus_web_1_0_app\Service\ContactService;
use Drupal\Tests\BrowserTestBase;
use Drupal\user\Entity\User;

/**
 * Tests the ContactsController.
 */
class ContactsControllerTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['htmx_plus_web_1_0_app'];

  /**
   * A user with permission to access the contacts page.
   *
   * @var \Drupal\user\Entity\User
   */
  protected $privilegedUser;

  /**
   * Base URL for contacts.
   */
  private const BASE_CONTACTS_URL = '/contacts';

  /**
   * The contact service mock.
   *
   * @var \Drupal\htmx_plus_web_1_0_app\Service\ContactService|\PHPUnit\Framework\MockObject\MockObject
   */
  private $contactService;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $user = $this->drupalCreateUser([
      'access content',
    ]);

    if ($user instanceof User) {
      $this->privilegedUser = $user;
      $this->drupalLogin($user);
    }

    // Mock the ContactService.
    $this->contactService = $this->createMock(ContactService::class);
    $this->container->set('htmx_plus_web_1_0_app.contact_service', $this->contactService);
  }

  /**
   * Tests the /contacts route.
   */
  public function testNewContactGetRequest(): void {
    $this->drupalGet(sprintf('%s/new', self::BASE_CONTACTS_URL));

    $this->assertSession()->statusCodeEquals(200);

    $this->assertSession()->elementExists('css', 'form');
    $this->assertSession()->pageTextContains('New Contact');
  }

  /**
   * Tests the /contacts/new route with a POST request.
   */
  public function testNewContactPostRequestSuccess(): void {
    /* $this->contactService->expects($this->once())
    ->method('saveContact')
    ->with([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'phone' => '1234567890',
    ]); */

    $this->drupalGet(sprintf('%s/new', self::BASE_CONTACTS_URL));
    $this->submitForm([
      'name' => 'John Doe',
      'email' => 'john@example.com',
      'phone' => '1234567890',
    ], 'Save');

    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->addressEquals('/contacts');
  }

  /**
   * Tests the /contacts/new route with a POST request that fails validation.
   */
  public function testNewContactPostRequestValidationError(): void {
    $this->drupalGet(sprintf('%s/new', self::BASE_CONTACTS_URL));
    $this->submitForm([
      'name' => '',
      'email' => '',
      'phone' => '',
    ], 'Save');

    $this->assertSession()->statusCodeEquals(200);

    $this->assertSession()->elementExists('css', '.error');
    $this->assertSession()->pageTextContains('Name is required');
    $this->assertSession()->pageTextContains('Email is required');
  }

  /**
   * Tests the /contacts/{contact_id}/edit route.
   */
  public function testEditContactGetRequest(): void {
    $contact_id = $this->createContact();

    $this->drupalGet(sprintf('%s/%d/edit', self::BASE_CONTACTS_URL, $contact_id));

    $this->assertSession()->statusCodeEquals(200);

    $this->assertSession()->elementExists('css', 'form');
    $this->assertSession()->pageTextContains('Edit Contact');
  }

  /**
   * Tests the /contacts/{contact_id}/edit route with a POST request.
   */
  public function testEditContactPostRequestSuccess(): void {
    $contact_id = $this->createContact();

    $this->drupalGet(sprintf('%s/%d/edit', self::BASE_CONTACTS_URL, $contact_id));
    $this->submitForm([
      'name' => 'Jane Smith',
      'email' => 'jane.smith@example.com',
      'phone' => '0987654321',
    ], 'Save');

    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->addressEquals(sprintf('/contacts/%d', $contact_id));
  }

  /**
   * Tests the /contacts/{contact_id}/edit route that fails validation.
   */
  public function testEditContactPostRequestValidationError(): void {
    $contact_id = $this->createContact();

    $this->drupalGet(sprintf('%s/%d/edit', self::BASE_CONTACTS_URL, $contact_id));
    $this->submitForm([
      'name' => '',
      'email' => '',
      'phone' => '',
    ], 'Save');

    $this->assertSession()->statusCodeEquals(200);

    $this->assertSession()->elementExists('css', '.error');
    $this->assertSession()->pageTextContains('Name is required');
    $this->assertSession()->pageTextContains('Email is required');
  }

  /**
   * Tests the /contacts/{contact_id}/edit route for a non-existent contact.
   */
  public function testEditNonExistentContact(): void {
    $non_existent_contact_id = 999;

    $this->drupalGet(sprintf('%s/%d/edit', self::BASE_CONTACTS_URL, $non_existent_contact_id));
    $this->assertSession()->statusCodeEquals(404);
  }

  /**
   * Tests the /contacts/{contact_id}/delete route with a POST request.
   */
  public function testDeleteContactPostRequestSuccess(): void {
    // @todo write a test.
  }

  /**
   * Helper function to create a contact.
   */
  private function createContact(): string {
    $connection = \Drupal::database();
    $connection->insert('contacts')
      ->fields([
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'phone' => '0987654321',
      ])
      ->execute();

    return $connection->lastInsertId();
  }

}
