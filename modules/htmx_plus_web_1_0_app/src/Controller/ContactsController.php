<?php

declare(strict_types=1);

namespace Drupal\htmx_plus_web_1_0_app\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\htmx_plus\Service\RequestParameterService;
use Drupal\htmx_plus_web_1_0_app\Handler\ContactEditRequestHandler;
use Drupal\htmx_plus_web_1_0_app\Repository\ContactRepository;
use Drupal\htmx_plus_web_1_0_app\Service\ContactDataExtractor;
use Drupal\htmx_plus_web_1_0_app\Service\ContactsRenderer;
use Drupal\htmx_plus_web_1_0_app\Service\PostRequestValidator;
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
   * @param \Drupal\htmx_plus_web_1_0_app\Repository\ContactRepository $contactRepository
   *   The contact repository.
   * @param \Drupal\htmx_plus_web_1_0_app\Service\ContactDataExtractor $contactDataExtractor
   *   The contact data extractor.
   * @param \Drupal\htmx_plus_web_1_0_app\Service\PostRequestValidator $postRequestValidator
   *   The post request validator.
   * @param \Drupal\htmx_plus_web_1_0_app\Service\ContactsRenderer $contactsRenderer
   *   The contacts renderer.
   * @param \Drupal\htmx_plus\Service\RequestParameterService $requestParameterService
   *   The request parameter service.
   * @param \Drupal\htmx_plus_web_1_0_app\Handler\ContactEditRequestHandler $contactEditRequestHandler
   *   The contact edit request handler.
   */
  public function __construct(
    private ContactRepository $contactRepository,
    private ContactDataExtractor $contactDataExtractor,
    private PostRequestValidator $postRequestValidator,
    private ContactsRenderer $contactsRenderer,
    private RequestParameterService $requestParameterService,
    private ContactEditRequestHandler $contactEditRequestHandler,
  ) {
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
      return [
        '#theme' => 'contacts_new',
        '#contact' => [],
        '#validationResult' => [],
      ];
    }

    $contactData = $this->contactDataExtractor->getContactDataFromPostRequest($request);
    $validationResult = $this->postRequestValidator->validateContactData($contactData);

    if (!$validationResult->hasErrors()) {
      $this->contactRepository->saveContact($contactData);

      $url = Url::fromRoute('htmx_plus_web_1_0_app.contacts')->toString();
      return new RedirectResponse($url);
    }

    return [
      '#theme' => 'contacts_new',
      '#contact' => $contactData,
      '#validationResult' => $validationResult,
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
  #[Route('/contacts/{contact_id}', name: 'contact_show')]
  public function show(string $contact_id): array {
    $contact = $this->contactRepository->getContactById($contact_id);

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

    $contact = $this->contactRepository->getContactById($contact_id);
    if (NULL === $contact) {
      throw new NotFoundHttpException();
    }

    $this->contactRepository->deleteContact($contact_id);

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
    $contacts = $this->contactRepository->all();

    return $this->contactsRenderer->renderContactsList($request, $contacts);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new static(
      $container->get('htmx_plus_web_1_0_app.contact_repository'),
      $container->get('htmx_plus_web_1_0_app.contact_data_extractor'),
      $container->get('htmx_plus_web_1_0_app.post_request_validator'),
      $container->get('htmx_plus_web_1_0_app.contacts_renderer'),
      $container->get('htmx_plus.request_parameters_service'),
      $container->get('htmx_plus_web_1_0_app.contact_edit_request_handler'),
    );
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
      return $this->contactRepository->search($search);
    }

    return $this->contactRepository->all();

  }

}
