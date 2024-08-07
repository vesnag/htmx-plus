<?php

declare(strict_types=1);

namespace Drupal\htmx_plus_web_1_0_app\Service;

use Drupal\Core\Render\RendererInterface;
use Drupal\htmx_plus\Service\HtmxRequestChecker;
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
   * Renders the contacts list.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   * @param array<int,array<string, mixed>> $contacts
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

}
