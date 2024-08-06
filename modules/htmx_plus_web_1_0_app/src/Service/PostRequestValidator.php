<?php

declare(strict_types=1);

namespace Drupal\htmx_plus_web_1_0_app\Service;

use Drupal\htmx_plus_web_1_0_app\Model\ContactData;
use Drupal\htmx_plus_web_1_0_app\Model\ValidationResult;

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
   * @return \Drupal\htmx_plus_web_1_0_app\Model\ValidationResult
   *   The validation result.
   */
  public function validateContactData(ContactData $contact_data): ValidationResult {
    $validationResult = new ValidationResult();

    if (empty($contact_data->getName())) {
      $validationResult->addError('name', 'Name is required.');
    }
    // @todo Validate also email format. For now it is not relevant
    if (empty($contact_data->getEmail())) {
      $validationResult->addError('email', 'Email is required.');
    }
    if (empty($contact_data->getPhone())) {
      $validationResult->addError('phone', 'Phone is required.');
    }

    return $validationResult;
  }

}
