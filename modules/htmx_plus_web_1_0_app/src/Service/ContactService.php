<?php

declare(strict_types=1);

namespace Drupal\htmx_plus_web_1_0_app\Service;

use Drupal\htmx_plus_web_1_0_app\Model\ContactData;
use Drupal\htmx_plus_web_1_0_app\Repository\ContactRepository;

/**
 * Service for handling contacts.
 */
class ContactService {

  /**
   * Constructs a ContactService object.
   *
   * @param \Drupal\htmx_plus_web_1_0_app\Repository\ContactRepository $contactRepository
   *   The contact repository.
   */
  public function __construct(
    private ContactRepository $contactRepository,
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
    return $this->contactRepository->search($search);
  }

  /**
   * Saves a contact.
   *
   * @param \Drupal\htmx_plus_web_1_0_app\Model\ContactData $contactData
   *   The contact data.
   */
  public function saveContact(ContactData $contactData): void {
    $this->contactRepository->saveContact($contactData);
  }

  /**
   * Retrieves contacts.
   *
   * @return array<int, array<string, mixed>>
   *   An array of all contacts, where each contact is an associative array.
   */
  public function getContacts(): array {
    return $this->contactRepository->getContacts();
  }

  /**
   * Retrieves a contact by its ID.
   *
   * @param string $contactId
   *   The contact ID.
   *
   * @return \Drupal\htmx_plus_web_1_0_app\Model\ContactData|null
   *   The contact data, or NULL if the contact does not exist.
   */
  public function getContactById(string $contactId): ?ContactData {
    return $this->contactRepository->getContactById($contactId);
  }

  /**
   * Updates a contact.
   *
   * @param \Drupal\htmx_plus_web_1_0_app\Model\ContactData $contactData
   *   The contact data.
   */
  public function updateContact(ContactData $contactData): void {
    $this->contactRepository->updateContact($contactData);
  }

  /**
   * Deletes a contact.
   *
   * @param string $contactId
   *   The contact ID.
   */
  public function deleteContact(string $contactId): void {
    $this->contactRepository->deleteContact($contactId);
  }

  /**
   * Checks if an email exists in the contacts table.
   *
   * @param string $email
   *   The email address to check.
   */
  public function doesEmailExist(string $email): bool {
    return $this->contactRepository->doesEmailExist($email);
  }

}
