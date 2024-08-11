<?php

declare(strict_types=1);

namespace Drupal\htmx_plus_web_1_0_app\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\htmx_plus_web_1_0_app\Service\EmailValidationService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for email validation.
 *
 * @phpstan-consistent-constructor
 */
class EmailValidationController extends ControllerBase {

  public function __construct(
    private EmailValidationService $emailValidationService,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('htmx_plus_web_1_0_app.email_validation_service')
    );
  }

  /**
   * Validate an email address.
   *
   * @param string $contact_id
   *   The contact ID.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   The response.
   */
  public function validateEmail(string $contact_id, Request $request): Response {
    $email = (string) $request->query->get('email');
    $error_message = $this->emailValidationService->validateEmail($email);

    return new Response($error_message);
  }

}
