<?php

declare(strict_types=1);

namespace Drupal\htmx_plus_web_1_0_app\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\htmx_plus\Service\RequestParameterService;
use Drupal\htmx_plus_web_1_0_app\Handler\ContactEditRequestHandler;
use Drupal\htmx_plus_web_1_0_app\Service\ContactExtractor;
use Drupal\htmx_plus_web_1_0_app\Service\ContactService;
use Drupal\htmx_plus_web_1_0_app\Service\ContactsRenderer;
use Drupal\htmx_plus_web_1_0_app\Service\ContactValidator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

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
   * @param \Drupal\htmx_plus_web_1_0_app\Service\ContactExtractor $contactExtractor
   *   The contact data extractor.
   * @param \Drupal\htmx_plus_web_1_0_app\Service\ContactValidator $contactValidator
   *   The contact data validator.
   * @param \Drupal\htmx_plus_web_1_0_app\Service\ContactsRenderer $contactsRenderer
   *   The contacts renderer.
   * @param \Drupal\htmx_plus\Service\RequestParameterService $requestParameterService
   *   The request parameter service.
   * @param \Drupal\htmx_plus_web_1_0_app\Handler\ContactEditRequestHandler $contactEditRequestHandler
   *   The contact edit request handler.
   */
  public function __construct(
    private ContactService $contactService,
    private ContactExtractor $contactExtractor,
    private ContactValidator $contactValidator,
    private ContactsRenderer $contactsRenderer,
    private RequestParameterService $requestParameterService,
    private ContactEditRequestHandler $contactEditRequestHandler,
  ) {
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new static(
      $container->get('htmx_plus_web_1_0_app.contact_service'),
      $container->get('htmx_plus_web_1_0_app.contact_extractor'),
      $container->get('htmx_plus_web_1_0_app.contact_validator'),
      $container->get('htmx_plus_web_1_0_app.contacts_renderer'),
      $container->get('htmx_plus.request_parameters_service'),
      $container->get('htmx_plus_web_1_0_app.contact_edit_request_handler'),
    );
  }

  /**
   * Handles the /contacts route.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return array<string,mixed>|\Symfony\Component\HttpFoundation\Response
   *   A render array.
   */
  #[Route('/contacts', name: 'contacts')]
  public function contacts(Request $request): Response|array {
    $contacts = $this->getContacts($request);

    return $this->contactsRenderer->renderContactPage($request, $contacts);
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
  #[Route('/contacts/new', name: 'contacts_new')]
  public function new(Request $request): array|RedirectResponse {
    if ('POST' !== $request->getMethod()) {
      return $this->contactsRenderer->renderNewContactForm();
    }

    $contactFromPost = $this->contactExtractor->getContactFromPostRequest($request);
    $validationResult = $this->contactValidator->validateContact($contactFromPost);

    if (!$validationResult->hasErrors()) {
      $this->contactService->saveContact($contactFromPost);

      $url = Url::fromRoute('htmx_plus_web_1_0_app.contacts')->toString();
      return new RedirectResponse($url);
    }

    return $this->contactsRenderer->renderNewContactForm($contactFromPost, $validationResult);

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
  #[Route('/contacts/{contact_id}', name: 'contact_show')]
  public function show(string $contact_id): array {
    $contact = $this->contactService->getContactById($contact_id);

    if (NULL === $contact) {
      throw new NotFoundHttpException();
    }

    return $this->contactsRenderer->renderContactShow($contact);
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
  #[Route('/contacts/{contact_id}/edit', name: 'contact_edit')]
  public function edit(string $contact_id, Request $request): array|RedirectResponse {
    if (!in_array($request->getMethod(), ['GET', 'POST', 'DELETE'])) {
      throw new MethodNotAllowedHttpException(['GET', 'POST', 'DELETE'], 'Method Not Allowed');
    }

    if ('POST' === $request->getMethod()) {
      return $this->contactEditRequestHandler->handlePost($contact_id, $request);
    }

    if ('DELETE' === $request->getMethod()) {
      return $this->contactEditRequestHandler->handleDelete($contact_id);
    }

    return $this->contactEditRequestHandler->handleGet($contact_id);
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
  #[Route('/contacts/{contact_id}/delete', name: 'contact_delete')]
  public function delete(string $contact_id, Request $request): array|RedirectResponse {
    if ('POST' !== $request->getMethod()) {
      throw new MethodNotAllowedHttpException(['POST'], 'Method Not Allowed');
    }

    $contact = $this->contactService->getContactById($contact_id);
    if (NULL === $contact) {
      throw new NotFoundHttpException();
    }

    $this->contactService->deleteContact($contact_id);

    $url = Url::fromRoute('htmx_plus_web_1_0_app.contacts')->toString();
    return new RedirectResponse($url);
  }

  /**
   * Returns a list of contacts.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return array<string,mixed>|\Symfony\Component\HttpFoundation\Response
   *   A render array.
   */
  #[Route('/contacts/list', name: 'contacts_list')]
  public function builContactList(Request $request): Response|array {
    $contacts = $this->contactService->getContacts();

    return $this->contactsRenderer->renderContactsList($request, $contacts);
  }

  /**
   * Gets the contacts set based on the search query.
   *
   * @return mixed[]
   *   The contacts set.
   */
  private function getContacts(Request $request): array {
    $search = $this->requestParameterService->getRequestParameter($request, 'q');

    if ('' !== $search) {
      return $this->contactService->search($search);
    }

    return $this->contactService->getContacts();

  }

}
