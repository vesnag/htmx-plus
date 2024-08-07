<?php

declare(strict_types=1);

namespace Drupal\htmx_plus\Service;

use Symfony\Component\HttpFoundation\Request;

/**
 * Service for checking if a request is an htmx request.
 */
class HtmxRequestChecker {

  /**
   * Checks if a request is an htmx request.
   */
  public function isHtmxRequest(Request $request): bool {
    return $request->headers->has('Hx-Request');
  }

}
