<?php

declare(strict_types=1);

namespace Drupal\htmx_plus_web_1_0_app\Repository;

use Drupal\Core\Database\Connection;
use Drupal\htmx_plus_web_1_0_app\Model\ContactData;

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
   * @return array<int, array<string, mixed>>
   *   An array of contacts, where each contact is an associative array.
   */
  public function search(string $search): array {
    $query = $this->database->select('contacts', 'c')
      ->fields('c')
      ->condition('name', '%' . $this->database->escapeLike($search) . '%', 'LIKE');

    /** @var \Drupal\Core\Database\StatementInterface|array[] $statement */
    $statement = $query->execute();
    $result = $statement->fetchAll();

    return $result;
  }

  /**
   * Saves a contact.
   *
   * @param \Drupal\htmx_plus_web_1_0_app\Model\ContactData $contact_data
   *   The contact data.
   */
  public function saveContact(ContactData $contact_data): void {
    $this->database->insert('contacts')
      ->fields([
        'name' => $contact_data->getName(),
        'email' => $contact_data->getEmail(),
        'phone' => $contact_data->getPhone(),
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
   * @return array<int, array<string, mixed>>
   *   An array of contacts, where each contact is an associative array.
   */
  public function getContacts(?int $limit = NULL, ?int $offset = NULL): array {
    $query = $this->database->select('contacts', 'c')
      ->fields('c');

    if ($limit !== NULL && $offset !== NULL) {
      $query->range($offset, $limit);
    }

    /** @var \Drupal\Core\Database\StatementInterface|array[] $statement */
    $statement = $query->execute();
    $result = $statement->fetchAll();

    return $result;
  }

  /**
   * Retrieves a contact by its ID.
   *
   * @param string $contact_id
   *   The contact ID.
   *
   * @return \Drupal\htmx_plus_web_1_0_app\Model\ContactData|null
   *   The contact data, or NULL if the contact does not exist.
   */
  public function getContactById(string $contact_id): ?ContactData {
    $query = $this->database->select('contacts', 'c')
      ->fields('c', ['id', 'name', 'email', 'phone'])
      ->condition('id', $this->database->escapeLike($contact_id));

    /** @var \Drupal\Core\Database\StatementInterface|array[] $statement */
    $statement = $query->execute();
    $contact = $statement->fetchAssoc();

    if (FALSE === $contact) {
      return NULL;
    }

    return is_array($contact) ? new ContactData(
      $contact['name'],
      $contact['email'],
      $contact['phone'],
      (string) $contact['id']
      ) : NULL;
  }

  /**
   * Updates a contact.
   *
   * @param \Drupal\htmx_plus_web_1_0_app\Model\ContactData $contactData
   *   The contact data.
   */
  public function updateContact(ContactData $contactData): void {
    $this->database->update('contacts')
      ->fields([
        'name' => $contactData->getName(),
        'email' => $contactData->getEmail(),
        'phone' => $contactData->getPhone(),
      ])
      ->condition('id', $contactData->id())
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

}
