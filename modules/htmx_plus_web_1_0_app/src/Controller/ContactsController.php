<?php

declare(strict_types=1);

namespace Drupal\htmx_plus_web_1_0_app\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\htmx_plus_web_1_0_app\Service\ContactDataExtractor;
use Drupal\htmx_plus_web_1_0_app\Service\ContactService;
use Drupal\htmx_plus_web_1_0_app\Service\PostRequestValidator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controller for the Contacts page.
 *
 * @phpstan-consistent-constructor
 */
class ContactsController extends ControllerBase {

  /**
   * Constructs a ContactsController object.
   *
   * @param \Drupal\htmx_plus_web_1_0_app\Service\ContactService $contactService
   *   The contact service.
   * @param \Drupal\htmx_plus_web_1_0_app\Service\ContactDataExtractor $contactDataExtractor
   *   The contact data extractor.
   * @param \Drupal\htmx_plus_web_1_0_app\Service\PostRequestValidator $postRequestValidator
   *   The post request validator.
   */
  public function __construct(
    protected ContactService $contactService,
    protected ContactDataExtractor $contactDataExtractor,
    protected PostRequestValidator $postRequestValidator,
  ) {
  }

  /**
   * Handles the /contacts route.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return array<string,mixed>
   *   A render array.
   */
  public function contacts(Request $request): array {
    $search = (string) $request->query->get('q');
    if ('' !== $search) {
      $contacts_set = $this->contactService->search($search);
    }
    else {
      $contacts_set = $this->contactService->all();
    }

    return [
      '#theme' => 'contacts_page',
      '#contacts' => $contacts_set,
    ];
  }

  /**
   * Handles the GET and POST requests for creating a new contact.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return array<string,mixed>|\Symfony\Component\HttpFoundation\RedirectResponse
   *   A render array or a redirect response.
   */
  public function new(Request $request): array|RedirectResponse {
    if (FALSE === $request->isMethod('post')) {
      throw new MethodNotAllowedHttpException(['POST'], 'Method not allowed');
    }

    $contactData = $this->contactDataExtractor->getContactDataFromPostRequest($request);
    $validationResult = $this->postRequestValidator->validateContactData($contactData);

    if (!$validationResult->hasErrors()) {
      $this->contactService->saveContact($contactData);

      $url = Url::fromRoute('htmx_plus_web_1_0_app.contacts')->toString();
      return new RedirectResponse($url);
    }

    return [
      '#theme' => 'contacts_new',
      '#contact' => $contactData,
      '#errors' => $validationResult->getErrors(),
    ];
  }

  /**
   * Shows a single contact based on the contact ID.
   *
   * @param string $contact_id
   *   The contact ID.
   *
   * @return array<string,mixed>
   *   A render array.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
   */
  public function show(string $contact_id): array {
    $contact = $this->contactService->getContactById($contact_id);

    if (NULL === $contact) {
      throw new NotFoundHttpException();
    }

    return [
      '#theme' => 'contact_show',
      '#contact' => $contact,
    ];
  }

  /**
   * Handles the GET and POST requests for editing a contact.
   *
   * @param string $contact_id
   *   The contact ID.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return array<string,mixed>|\Symfony\Component\HttpFoundation\RedirectResponse
   *   A render array or a redirect response.
   */
  public function edit(string $contact_id, Request $request): array|RedirectResponse {
    if (FALSE === $request->isMethod('post')) {
      throw new MethodNotAllowedHttpException(['POST'], 'Method not allowed');
    }

    $contact = $this->contactService->getContactById($contact_id);

    if (NULL === $contact) {
      throw new NotFoundHttpException();
    }

    $contactData = $this->contactDataExtractor->getContactDataFromPostRequest($request);
    $contactData->setId($contact_id);

    $validationResult = $this->postRequestValidator->validateContactData($contactData);

    if ($validationResult->hasErrors()) {
      return [
        '#theme' => 'contact_edit',
        '#contact' => $contactData,
        '#errors' => $validationResult->getErrors(),
      ];
    }

    $this->contactService->updateContact($contactData);

    $url = Url::fromRoute('htmx_plus_web_1_0_app.contact_show', [
      'contact_id' => $contact_id,
    ])->toString();
    return new RedirectResponse($url);
  }

  /**
   * Handles the GET and POST requests for deleting a contact.
   *
   * @param string $contact_id
   *   The contact ID.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return array<string, mixed>|\Symfony\Component\HttpFoundation\RedirectResponse
   *   A render array or a redirect response.
   */
  public function delete(string $contact_id, Request $request): array|RedirectResponse {
    $contact = $this->contactService->getContactById($contact_id);

    if (NULL === $contact) {
      throw new NotFoundHttpException();
    }

    if (TRUE === $request->isMethod('post')) {
      $this->contactService->deleteContact($contact_id);

      $url = Url::fromRoute('htmx_plus_web_1_0_app.contacts')->toString();
      return new RedirectResponse($url);
    }

    $url = Url::fromRoute('htmx_plus_web_1_0_app.contact_edit', [
      'contact_id' => $contact_id,
    ])->toString();
    return new RedirectResponse($url);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new static(
      $container->get('htmx_plus_web_1_0_app.contact_service'),
      $container->get('htmx_plus_web_1_0_app.contact_data_extractor'),
      $container->get('htmx_plus_web_1_0_app.post_request_validator'),
    );
  }

}
