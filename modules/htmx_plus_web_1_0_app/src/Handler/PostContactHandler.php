<?php

declare(strict_types=1);

namespace Drupal\htmx_plus_web_1_0_app\Handler;

use Drupal\Core\Url;
use Drupal\htmx_plus_web_1_0_app\Repository\ContactRepository;
use Drupal\htmx_plus_web_1_0_app\Service\ContactExtractor;
use Drupal\htmx_plus_web_1_0_app\Service\ContactValidator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Handler for updating a contact.
 */
class PostContactHandler {

  public function __construct(
    private readonly ContactRepository $contactRepository,
    private readonly ContactExtractor $contactExtractor,
    private readonly ContactValidator $contactValidator,
  ) {}

  /**
   * Handles the update of a contact.
   *
   * @param string $contact_id
   *   The contact ID.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return array<string,mixed>|\Symfony\Component\HttpFoundation\RedirectResponse
   *   A render array or a redirect response.
   */
  public function handle(string $contact_id, Request $request): array|RedirectResponse {
    $contact = $this->contactExtractor->getContactFromPostRequest($request);
    $contact->setId($contact_id);

    $validationResult = $this->contactValidator->validateContact($contact);

    if ($validationResult->hasErrors()) {
      return [
        '#theme' => 'contact_edit',
        '#contact' => $contact,
        '#validationResult' => $validationResult,
      ];
    }

    $this->contactRepository->updateContact($contact);

    $url = Url::fromRoute('htmx_plus_web_1_0_app.contact_show', [
      'contact_id' => $contact_id,
    ])->toString();
    return new RedirectResponse($url);
  }

}
