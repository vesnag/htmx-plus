<?php

declare(strict_types=1);

namespace Drupal\htmx_plus_web_1_0_app\Service;

use Drupal\htmx_plus\Service\RequestParameterService;
use Drupal\htmx_plus_web_1_0_app\Model\Contact;
use Drupal\htmx_plus_web_1_0_app\Repository\ContactRepository;
use Symfony\Component\HttpFoundation\Request;

/**
 * Service for handling contacts.
 */
class ContactService {

  /**
   * Constructs a ContactService object.
   *
   * @param \Drupal\htmx_plus_web_1_0_app\Repository\ContactRepository $contactRepository
   *   The contact repository.
   * @param \Drupal\htmx_plus\Service\RequestParameterService $requestParameterService
   *   The request parameter service.
   */
  public function __construct(
    private ContactRepository $contactRepository,
    private RequestParameterService $requestParameterService,
  ) {}

  /**
   * Gets the contacts set based on the search query.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return mixed[]
   *   The contacts set.
   */
  public function getContactsBySearchQuery(Request $request): array {
    $search = $this->requestParameterService->getRequestParameter($request, 'q');

    if ('' !== $search) {
      return $this->search($search);
    }

    return $this->getContacts();
  }

  /**
   * Saves a contact.
   *
   * @param \Drupal\htmx_plus_web_1_0_app\Model\Contact $contact
   *   The contact data.
   */
  public function saveContact(Contact $contact): void {
    $this->contactRepository->saveContact($contact);
  }

  /**
   * Retrieves contacts.
   *
   * @return \Drupal\htmx_plus_web_1_0_app\Model\Contact[]
   *   An array of contacts.
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
   * @return \Drupal\htmx_plus_web_1_0_app\Model\Contact|null
   *   The contact data, or NULL if the contact does not exist.
   */
  public function getContactById(string $contactId): ?Contact {
    return $this->contactRepository->getContactById($contactId);
  }

  /**
   * Updates a contact.
   *
   * @param \Drupal\htmx_plus_web_1_0_app\Model\Contact $contact
   *   The contact data.
   */
  public function updateContact(Contact $contact): void {
    $this->contactRepository->updateContact($contact);
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

  /**
   * Searches for contacts based on criteria.
   *
   * @param string $search
   *   The search query.
   *
   * @return \Drupal\htmx_plus_web_1_0_app\Model\Contact[]
   *   An array of contacts.
   */
  private function search(string $search): array {
    return $this->contactRepository->search($search);
  }

}
