<?php

declare(strict_types=1);

namespace Tests\Helper;

use Drupal\Core\Database\Connection;
use Drupal\htmx_plus_web_1_0_app\Model\Contact;

/**
 * Helper class for generating and inserting Contact data.
 */
class ContactHelper {

  public function __construct(private Connection $database) {}

  /**
   * Generate an array of Contact objects.
   *
   * @param int $count
   *   The number of Contact objects to generate.
   *
   * @return \Drupal\htmx_plus_web_1_0_app\Model\Contact[]
   *   An array of Contact objects.
   */
  public function generateContacts(int $count): array {
    return array_map(fn($i) => new Contact(
        name: sprintf('Test Contact %d', $i),
        email: sprintf('test%d@example.com', $i),
        phone: sprintf('123456789%d', $i),
        id: (string) $i
    ), range(1, $count));
  }

  /**
   * Insert Contact objects into the database.
   *
   * @param \Drupal\htmx_plus_web_1_0_app\Model\Contact[] $contacts
   *   An array of Contact objects to insert.
   */
  public function insertContactsIntoDatabase(array $contacts): void {
    $query = $this->database->insert('contacts')
      ->fields(['id', 'name', 'email', 'phone']);

    foreach ($contacts as $contact) {
      $query->values([
        'id' => $contact->id(),
        'name' => $contact->getName(),
        'email' => $contact->getEmail(),
        'phone' => $contact->getPhone(),
      ]);
    }

    $query->execute();
  }

  /**
   * Truncate the contacts table.
   */
  public function truncateContactsTable(): void {
    $this->database->truncate('contacts')->execute();
  }

}
