<?php

declare(strict_types=1);

namespace Drupal\htmx_plus_web_1_0_app\Model;

/**
 * Represents contact data.
 */
final class ContactData {

  /**
   * The name.
   */
  private string $name;

  /**
   * The email.
   */
  private string $email;

  /**
   * The phone.
   */
  private string $phone;

  public function __construct(string $name, string $email, string $phone) {
    $this->name = $name;
    $this->email = $email;
    $this->phone = $phone;
  }

  /**
   * Get the name.
   */
  public function getName(): string {
    return $this->name;
  }

  /**
   * Get the email.
   */
  public function getEmail(): string {
    return $this->email;
  }

  /**
   * Get the phone.
   */
  public function getPhone(): string {
    return $this->phone;
  }

}
