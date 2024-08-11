<?php

namespace Drupal\htmx_plus_web_1_0_app\Service;

use Drupal\htmx_plus_web_1_0_app\Repository\ContactRepository;

/**
 * Service for validating email addresses.
 */
class EmailValidationService {

  public function __construct(
    private ContactRepository $contactRepository,
  ) {}

  /**
   * Validates an email address.
   */
  public function validateEmail(string $email): string {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      return 'Invalid email format.';
    }

    if ($this->contactRepository->doesEmailExist($email)) {
      return 'Email already exists.';
    }

    return '';
  }

}
