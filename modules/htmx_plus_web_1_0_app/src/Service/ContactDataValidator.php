<?php

declare(strict_types=1);

namespace Drupal\htmx_plus_web_1_0_app\Service;

use Drupal\htmx_plus_web_1_0_app\Model\ContactData;
use Drupal\htmx_plus_web_1_0_app\Model\ValidationResult;

/**
 * Validates contact data.
 */
class ContactDataValidator {

  public function __construct(
    private EmailValidationService $emailValidationService,
  ) {}

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

    $this->validateEmail($contact_data->getEmail(), $validationResult);

    if (empty($contact_data->getPhone())) {
      $validationResult->addError('phone', 'Phone is required.');
    }

    return $validationResult;
  }

  /**
   * Validate the email and add errors to the validation result if necessary.
   *
   * @param string|null $email
   *   The email to validate.
   * @param \Drupal\htmx_plus_web_1_0_app\Model\ValidationResult $validationResult
   *   The validation result to add errors to.
   */
  private function validateEmail(?string $email, ValidationResult $validationResult): void {
    if (empty($email)) {
      $validationResult->addError('email', 'Email is required.');
      return;
    }

    $emailValidationMessage = $this->emailValidationService->validateEmail($email);
    if ($emailValidationMessage !== '') {
      $validationResult->addError('email', $emailValidationMessage);
    }
  }

}
