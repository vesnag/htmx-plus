<?php

declare(strict_types=1);

namespace Drupal\htmx_plus_web_1_0_app\Service;

use Drupal\Core\Render\RendererInterface;
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
    $render_array = [
      '#theme' => 'contacts_page',
      '#contacts' => $contacts,
    ];

    if (TRUE === $this->htmxRequestChecker->isHtmxRequest($request)) {
      $html_cotnent = $this->renderer->render($render_array)->__toString();
      return new Response($html_cotnent);
    }

    return $render_array;
  }

}
