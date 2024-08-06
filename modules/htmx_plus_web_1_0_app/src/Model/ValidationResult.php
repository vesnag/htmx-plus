<?php

declare(strict_types=1);

namespace Drupal\htmx_plus_web_1_0_app\Model;

/**
 * Represents the result of a validation operation.
 */
class ValidationResult {

  /**
   * The validation errors.
   *
   * @var ValidationError[]
   *   The validation errors.
   */
  private array $errors = [];

  /**
   * Add a validation error.
   *
   * @param string $field
   *   The field that has the error.
   * @param string $message
   *   The error message.
   */
  public function addError(string $field, string $message): void {
    $this->errors[] = new ValidationError($field, $message);
  }

  /**
   * Get the validation errors.
   *
   * @return ValidationError[]
   *   The validation errors.
   */
  public function getErrors(): array {
    return $this->errors;
  }

  /**
   * Check if there are validation errors.
   *
   * @return bool
   *   TRUE if there are validation errors, FALSE otherwise.
   */
  public function hasErrors(): bool {
    return !empty($this->errors);
  }

}
