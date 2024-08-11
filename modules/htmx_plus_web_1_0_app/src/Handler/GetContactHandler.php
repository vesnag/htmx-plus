<?php

declare(strict_types=1);

namespace Drupal\htmx_plus_web_1_0_app\Handler;

use Drupal\htmx_plus_web_1_0_app\Repository\ContactRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Handler for getting a contact.
 */
class GetContactHandler {

  public function __construct(
    private readonly ContactRepository $contactRepository,
  ) {}

  /**
   * Handles the retrieval of a contact.
   *
   * @param string $contact_id
   *   The contact ID.
   *
   * @return array<string,mixed>
   *   A render array.
   */
  public function handle(string $contact_id): array {
    $contact = $this->contactRepository->getContactById($contact_id);
    if (NULL === $contact) {
      throw new NotFoundHttpException();
    }

    return [
      '#theme' => 'contact_edit',
      '#contact' => $contact,
      '#validationResult' => [],
    ];
  }

}
