<?php

declare(strict_types=1);

namespace Drupal\htmx_plus_web_1_0_app\Service;

use Drupal\Core\Render\RendererInterface;
use Drupal\htmx_plus\Service\HtmxRequestChecker;
use Drupal\htmx_plus_web_1_0_app\Model\Contact;
use Drupal\htmx_plus_web_1_0_app\Model\ValidationResult;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Service for rendering the contacts page.
 */
final class ContactsRenderer {

  public function __construct(
    private HtmxRequestChecker $htmxRequestChecker,
    private RendererInterface $renderer,
  ) {}

  /**
   * Renders the contacts page.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   * @param array<string,mixed> $contacts
   *   The contacts to render.
   *
   * @return \Symfony\Component\HttpFoundation\Response|array<string,mixed>
   *   A render array or a response object.
   */
  public function renderContactPage(Request $request, array $contacts): Response|array {
    return [
      '#theme' => 'contacts_page',
      '#contacts' => $contacts,
      '#search_query' => (string) $request->query->get('q', $request->request->get('q', '')),
      '#cache' => [
        'contexts' => ['request_type'],
      ],
    ];
  }

  /**
   * Renders the contacts list.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   * @param \Drupal\htmx_plus_web_1_0_app\Model\Contact[] $contacts
   *   The contacts to render.
   *
   * @return \Symfony\Component\HttpFoundation\Response|array<string,mixed>
   *   A render array or a response object.
   */
  public function renderContactsList(Request $request, array $contacts): Response|array {
    $render_array = [
      '#theme' => 'contacts_list',
      '#contacts' => $contacts,
      '#cache' => [
        'contexts' => ['request_type'],
      ],
    ];

    if (TRUE === $this->htmxRequestChecker->isHtmxRequest($request)) {
      $html_content = $this->renderer->render($render_array)->__toString();
      return new Response($html_content);
    }

    return $render_array;
  }

  /**
   * Renders the new contact form.
   *
   * @param \Drupal\htmx_plus_web_1_0_app\Model\Contact $contact
   *   The contact data.
   * @param \Drupal\htmx_plus_web_1_0_app\Model\ValidationResult $validationResult
   *   The validation result.
   *
   * @return array<string,mixed>
   *   A render array.
   */
  public function renderNewContactForm(?Contact $contact = NULL, ?ValidationResult $validationResult = NULL): array {
    return [
      '#theme' => 'contacts_new',
      '#contact' => $contact,
      '#validationResult' => $validationResult,
    ];
  }

  /**
   * Renders the contact show page.
   *
   * @param \Drupal\htmx_plus_web_1_0_app\Model\Contact $contact
   *   The contact data.
   *
   * @return array<string,mixed>
   *   A render array.
   */
  public function renderContactShow(Contact $contact): array {
    return [
      '#theme' => 'contact_show',
      '#contact' => $contact,
    ];
  }

}
