<?php

namespace Drupal\htmx_plus\EventSubscriber;

use Drupal\Core\Render\AttachmentsInterface;
use Drupal\htmx_plus\Service\ConfigService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Debug Library Subscriber.
 */
class HtmxDebugLibrarySubscriber implements EventSubscriberInterface {

  public function __construct(
    protected ConfigService $configService,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [];
    $events[KernelEvents::RESPONSE][] = ['onResponse', 0];
    return $events;
  }

  /**
   * Event handler for KernelEvents::RESPONSE.
   *
   * @param \Symfony\Component\HttpKernel\Event\ResponseEvent $event
   *   The response event.
   */
  public function onResponse(ResponseEvent $event): void {
    if (FALSE === $this->configService->isDebugEnabled()) {
      return;
    }

    if (FALSE === $event->isMainRequest()) {
      return;
    }

    $response = $event->getResponse();

    if (!$response instanceof AttachmentsInterface) {
      return;
    }

    $attachments = $response->getAttachments();
    if (!isset($attachments['library'])) {
      $attachments['library'] = [];
    }
    $attachments['library'][] = 'htmx/debug';

    $response->setAttachments($attachments);
  }

}
