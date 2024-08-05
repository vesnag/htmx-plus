<?php

declare(strict_types=1);

namespace Drupal\htmx_plus_web_1_0_app\Service;

use Drupal\htmx_plus_web_1_0_app\Model\ContactData;

/**
 * Validates POST requests.
 */
class PostRequestValidator {

  /**
   * Extract and validate contact data from the request.
   *
   * @param \Drupal\htmx_plus_web_1_0_app\Model\ContactData $contact_data
   *   The contact data.
   *
   * @return array<string,string>
   *   An array of errors.
   */
  public function validateContactData(ContactData $contact_data): array {
    $errors = [];
    if (empty($contact_data->getName())) {
      $errors['name'] = 'Name is required.';
    }
    if (empty($contact_data->getEmail())) {
      $errors['email'] = 'Email is required.';
    }
    if (empty($contact_data->getPhone())) {
      $errors['phone'] = 'Phone is required.';
    }

    return $errors;
  }

}
