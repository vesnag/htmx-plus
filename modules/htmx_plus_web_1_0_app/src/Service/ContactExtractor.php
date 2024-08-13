<?php

declare(strict_types=1);

namespace Drupal\htmx_plus_web_1_0_app\Service;

use Drupal\htmx_plus_web_1_0_app\Model\Contact;
use Symfony\Component\HttpFoundation\Request;

/**
 * Extracts contact data from the request.
 */
class ContactExtractor {

  /**
   * Extract contact data from the request.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return \Drupal\htmx_plus_web_1_0_app\Model\Contact
   *   The contact data.
   *
   * @throws \InvalidArgumentException
   */
  public function getContactFromPostRequest(Request $request): Contact {
    if (FALSE === $request->isMethod('post')) {
      throw new \InvalidArgumentException('Invalid request method. Expected POST.');
    }

    return new Contact(
      (string) $request->request->get('name'),
      (string) $request->request->get('email'),
      (string) $request->request->get('phone')
    );
  }

}
