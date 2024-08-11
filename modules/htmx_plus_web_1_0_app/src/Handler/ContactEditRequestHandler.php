<?php

declare(strict_types=1);

namespace Drupal\htmx_plus_web_1_0_app\Handler;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Service for handling contact edit requests.
 */
class ContactEditRequestHandler {

  public function __construct(
    private readonly GetContactHandler $getContactHandler,
    private readonly PostContactHandler $postContactHandler,
    private readonly DeleteContactHandler $deleteContactHandler,
  ) {}

  /**
   * Handles the contact edit request.
   *
   * @param string $contact_id
   *   The contact ID.
   *
   * @return array<string,mixed>|\Symfony\Component\HttpFoundation\RedirectResponse
   *   A render array or a redirect response.
   */
  public function handleGet(string $contact_id): array|RedirectResponse {
    return $this->getContactHandler->handle($contact_id);
  }

  /**
   * Handles the POST request for editing a contact.
   *
   * @param string $contact_id
   *   The contact ID.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return array<string,mixed>|\Symfony\Component\HttpFoundation\RedirectResponse
   *   A render array or a redirect response.
   */
  public function handlePost(string $contact_id, Request $request): array|RedirectResponse {
    return $this->postContactHandler->handle($contact_id, $request);
  }

  /**
   * Handles the DELETE request for deleting a contact.
   */
  public function handleDelete(string $contact_id): RedirectResponse {
    return $this->deleteContactHandler->handle($contact_id);
  }

}
