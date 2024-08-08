<?php

declare(strict_types=1);

namespace Drupal\htmx_plus\Cache\Context;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Cache\Context\CacheContextInterface;
use Drupal\htmx_plus\Service\HtmxRequestChecker;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Cache context for request type.
 */
class RequestTypeCacheContext implements CacheContextInterface {

  private const CONTEXT_HTMX = 'htmx';
  private const CONTEXT_NON_HTMX = 'non_htmx';
  private const CONTEXT_NO_REQUEST = 'no_request';

  public function __construct(
    private RequestStack $requestStack,
    private HtmxRequestChecker $htmxRequestChecker,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function getLabel() {
    return (string) t('Request type');
  }

  /**
   * {@inheritdoc}
   */
  public function getContext() {
    $request = $this->requestStack->getCurrentRequest();
    if (NULL === $request) {
      return self::CONTEXT_NO_REQUEST;
    }

    if (TRUE === $this->htmxRequestChecker->isHtmxRequest($request)) {
      return self::CONTEXT_HTMX;
    }

    return self::CONTEXT_NON_HTMX;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheableMetadata() {
    return new CacheableMetadata();
  }

}
