<?php

declare(strict_types=1);

namespace Drupal\htmx_plus_web_1_0_app\Repository;

use Drupal\Core\Database\Connection;
use Drupal\htmx_plus_web_1_0_app\Model\Contact;

/**
 * Repository for handling contacts.
 */
class ContactRepository {

  /**
   * Constructs a ContactService object.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   */
  public function __construct(
    private Connection $database,
  ) {}

  /**
   * Searches for contacts based on criteria.
   *
   * @param string $search
   *   The search query.
   *
   * @return \Drupal\htmx_plus_web_1_0_app\Model\Contact[]
   *   An array of contacts.
   */
  public function search(string $search): array {
    $query = $this->database->select('contacts', 'c')
      ->fields('c')
      ->condition('name', '%' . $this->database->escapeLike($search) . '%', 'LIKE');

    /** @var \Drupal\Core\Database\StatementInterface|array[] $statement */
    $statement = $query->execute();
    $results = $statement->fetchAll();

    $contactObjects = [];
    foreach ($results as $record) {
      $contactObject = (object) $record;
      $contactObjects[] = $this->mapToContact($contactObject);
    }

    return $contactObjects;
  }

  /**
   * Saves a contact.
   *
   * @param \Drupal\htmx_plus_web_1_0_app\Model\Contact $contact
   *   The contact data.
   */
  public function saveContact(Contact $contact): void {
    $this->database->insert('contacts')
      ->fields([
        'name' => $contact->getName(),
        'email' => $contact->getEmail(),
        'phone' => $contact->getPhone(),
      ])
      ->execute();
  }

  /**
   * Retrieves contacts with optional pagination.
   *
   * @param int|null $limit
   *   The number of contacts to retrieve. If null, retrieves all contacts.
   * @param int|null $offset
   *   The offset for the contacts to retrieve.
   *   If null, retrieves from the beginning.
   *
   * @return \Drupal\htmx_plus_web_1_0_app\Model\Contact[]
   *   An array of contacts.
   */
  public function getContacts(?int $limit = NULL, ?int $offset = NULL): array {
    $query = $this->database->select('contacts', 'c')
      ->fields('c');

    if ($limit !== NULL && $offset !== NULL) {
      $query->range($offset, $limit);
    }

    /** @var \Drupal\Core\Database\StatementInterface|array[] $statement */
    $statement = $query->execute();
    $results = $statement->fetchAll();

    $contacts = array_map(function ($result) {
       return new Contact(
           $result->name,
           $result->email,
           $result->phone,
           $result->id
       );
    }, $results);

    return $contacts;
  }

  /**
   * Retrieves a contact by its ID.
   *
   * @param string $contact_id
   *   The contact ID.
   *
   * @return \Drupal\htmx_plus_web_1_0_app\Model\Contact|null
   *   The contact data, or NULL if the contact does not exist.
   */
  public function getContactById(string $contact_id): ?Contact {
    $query = $this->database->select('contacts', 'c')
      ->fields('c', ['id', 'name', 'email', 'phone'])
      ->condition('id', $this->database->escapeLike($contact_id));

    /** @var \Drupal\Core\Database\StatementInterface|array[] $statement */
    $statement = $query->execute();
    $contact = $statement->fetchAssoc();

    if (FALSE === $contact) {
      return NULL;
    }

    return $this->mapToContact((object) $contact);
  }

  /**
   * Updates a contact.
   *
   * @param \Drupal\htmx_plus_web_1_0_app\Model\Contact $contact
   *   The contact data.
   */
  public function updateContact(Contact $contact): void {
    $this->database->update('contacts')
      ->fields([
        'name' => $contact->getName(),
        'email' => $contact->getEmail(),
        'phone' => $contact->getPhone(),
      ])
      ->condition('id', $contact->id())
      ->execute();
  }

  /**
   * Deletes a contact.
   */
  public function deleteContact(string $contact_id): void {
    $this->database->delete('contacts')
      ->condition('id', $contact_id)
      ->execute();
  }

  /**
   * Checks if an email exists in the contacts table.
   *
   * @param string $email
   *   The email address to check.
   */
  public function doesEmailExist(string $email): bool {
    $query = $this->database->select('contacts', 'c')
      ->fields('c', ['email'])
      ->condition('email', $this->database->escapeLike($email));

    /** @var \Drupal\Core\Database\StatementInterface|array[] $statement */
    $statement = $query->execute();

    return (bool) $statement->fetchField();
  }

  /**
   * Maps a database record to a Contact object.
   *
   * @param object $record
   *   The database record.
   *
   * @return \Drupal\htmx_plus_web_1_0_app\Model\Contact
   *   The mapped Contact object.
   */
  private function mapToContact(object $record): Contact {
    return new Contact(
        name: $record->name ?? '',
        email: $record->email ?? '',
        phone: $record->phone ?? '',
        id: $record->id ?? NULL
    );
  }

}
