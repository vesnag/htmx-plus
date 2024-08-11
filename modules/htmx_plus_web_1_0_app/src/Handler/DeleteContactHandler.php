<?php

declare(strict_types=1);

namespace Drupal\htmx_plus_web_1_0_app\Handler;

use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\htmx_plus_web_1_0_app\Repository\ContactRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Handler for deleting a contact.
 */
class DeleteContactHandler {

  use StringTranslationTrait;

  public function __construct(
    private ContactRepository $contactRepository,
    private MessengerInterface $messenger,
  ) {}

  /**
   * Handles the deletion of a contact.
   */
  public function handle(string $contact_id): RedirectResponse {
    $this->contactRepository->deleteContact($contact_id);
    $this->messenger->addMessage($this->t('Contact has been successfully deleted.'));

    $url = Url::fromRoute('htmx_plus_web_1_0_app.contacts')->toString();
    return new RedirectResponse($url, Response::HTTP_SEE_OTHER);
  }

}
