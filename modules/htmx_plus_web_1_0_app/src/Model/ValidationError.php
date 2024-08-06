<?php

declare(strict_types=1);

namespace Drupal\htmx_plus_web_1_0_app\Model;

/**
 * Represents a validation error.
 */
class ValidationError {

  /**
   * The field that has the error.
   *
   * @var string
   *  The field that has the error.
   */
  private string $field;


  /**
   * The error message.
   *
   * @var string
   *  The error message.
   */
  private string $message;

  public function __construct(string $field, string $message) {
    $this->field = $field;
    $this->message = $message;
  }

  /**
   * Get the field that has the error.
   *
   * @return string
   *   The field that has the error.
   */
  public function getField(): string {
    return $this->field;
  }

  /**
   * Get the error message.
   *
   * @return string
   *   The error message.
   */
  public function getMessage(): string {
    return $this->message;
  }

}
