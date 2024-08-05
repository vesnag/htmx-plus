<?php

declare(strict_types=1);

namespace Drupal\htmx_plus_web_1_0_app\Service;

/**
 * Validates POST requests.
 */
class PostRequestValidator {

  /**
   * Extract and validate contact data from the request.
   *
   * @param array<string,mixed> $contact_data
   *   The contact data.
   *
   * @return array<string,string>
   *   An array of errors.
   */
  public function validateContactData(array $contact_data): array {
    $errors = [];
    if (empty($contact_data['name'])) {
      $errors['name'] = 'Name is required.';
    }
    if (empty($contact_data['email'])) {
      $errors['email'] = 'Email is required.';
    }
    if (empty($contact_data['phone'])) {
      $errors['phone'] = 'Phone is required.';
    }

    return $errors;
  }

}
