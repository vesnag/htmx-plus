<?php

declare(strict_types=1);

namespace Drupal\htmx_plus\Service;

use Symfony\Component\HttpFoundation\Request;

/**
 * Service for handling request parameters.
 */
class RequestParameterService {

  /**
   * Get a parameter based on the request method.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The HTTP request object.
   * @param string $parameter
   *   The parameter name.
   *
   * @return string
   *   The parameter value.
   */
  public function getRequestParameter(Request $request, string $parameter): string {
    if ($request->isMethod('POST')) {
      return (string) $request->request->get($parameter, '');
    }

    if ($request->isMethod('GET')) {
      return (string) $request->query->get($parameter, '');
    }

    return '';
  }

}
