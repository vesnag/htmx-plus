<?php

declare(strict_types=1);

namespace Drupal\htmx_plus_web_1_0_app\Service;

use Symfony\Component\HttpFoundation\Request;

/**
 * Extracts contact data from the request.
 */
class ContactDataExtractor {

  /**
   * Extract contact data from the request.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return array<string,string>
   *   The contact data.
   */
  public function getContactData(Request $request): array {
    if (FALSE === $request->isMethod('post')) {
      return [];
    }

    return [
      'name' => (string) $request->request->get('name'),
      'email' => (string) $request->request->get('email'),
      'phone' => (string) $request->request->get('phone'),
    ];
  }

}
