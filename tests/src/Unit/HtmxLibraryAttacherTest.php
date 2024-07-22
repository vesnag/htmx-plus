<?php

declare(strict_types=1);

namespace Drupal\Tests\htmx_plus\Unit;

use Drupal\Core\Asset\LibraryDiscoveryInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\htmx_plus\HtmxConditionVerifier;
use Drupal\htmx_plus\HtmxLibraryAttacher;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Psr\Log\LoggerInterface;

/**
 * Tests the HtmxLibraryAttacher class.
 *
 * @coversDefaultClass \Drupal\htmx_plus\HtmxLibraryAttacher
 *
 * @group htmx_plus
 */
class HtmxLibraryAttacherTest extends UnitTestCase {

  /**
   * The library discovery service.
   *
   * @var \Drupal\Core\Asset\LibraryDiscoveryInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $libraryDiscovery;

  /**
   * The HtmxConditionVerifier service.
   *
   * @var \Drupal\htmx_plus\HtmxConditionVerifier|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $htmxConditionVerifier;

  /**
   * The logger factory service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $loggerFactory;

  /**
   * The logger service.
   *
   * @var \Psr\Log\LoggerInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $logger;

  /**
   * The HtmxLibraryAttacher instance.
   *
   * @var \Drupal\htmx_plus\HtmxLibraryAttacher
   */
  protected $htmxLibraryAttacher;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->libraryDiscovery = $this->createMock(LibraryDiscoveryInterface::class);
    $this->htmxConditionVerifier = $this->createMock(HtmxConditionVerifier::class);
    $this->loggerFactory = $this->createMock(LoggerChannelFactoryInterface::class);
    $this->logger = $this->createMock(LoggerInterface::class);

    $this->loggerFactory->expects($this->any())
      ->method('get')
      ->willReturn($this->logger);

    $this->htmxLibraryAttacher = new HtmxLibraryAttacher(
      $this->libraryDiscovery,
      $this->htmxConditionVerifier,
      $this->loggerFactory
    );
  }

  /**
   * Tests the attachLibraryIfAvailable method.
   *
   * @param array<string,array<string,array<int,string>>> $renderArray
   *   The render array.
   * @param bool $forceAttach
   *   Whether to force the attachment of the library.
   * @param bool $doesLibraryExist
   *   Whether the library exists.
   * @param bool $shouldAttachLibrary
   *   Whether the library should be attached.
   * @param array<string,array<string,array<int,string>>> $expectedRenderArray
   *   The expected render array after attaching the library.
   */
  #[Test]
  #[DataProvider('attachLibraryIfAvailableDataProvider')]
  public function testAttachLibraryIfAvailable(array $renderArray, bool $forceAttach, bool $doesLibraryExist, bool $shouldAttachLibrary, array $expectedRenderArray): void {
    $this->libraryDiscovery->expects($this->any())
      ->method('getLibraryByName')
      ->willReturn($doesLibraryExist ? [] : FALSE);

    $this->htmxConditionVerifier->expects($this->any())
      ->method('shouldAttachHtmxLibrary')
      ->willReturn($shouldAttachLibrary);

    $this->htmxLibraryAttacher->attachLibraryIfAvailable($renderArray, $forceAttach);

    $this->assertEquals($expectedRenderArray, $renderArray);
  }

  /**
   * Data provider for the testAttachLibraryIfAvailable method.
   *
   * @return array<int,array<mixed>>
   *   An array of test data.
   */
  public static function attachLibraryIfAvailableDataProvider(): array {
    return [
      [
        ['#attached' => ['library' => []]],
        FALSE,
        TRUE,
        TRUE,
        ['#attached' => ['library' => ['htmx/drupal']]],
      ],
      [
        ['#attached' => ['library' => []]],
        FALSE,
        FALSE,
        TRUE,
        ['#attached' => ['library' => []]],
      ],
      [
        ['#attached' => ['library' => []]],
        FALSE,
        TRUE,
        FALSE,
        ['#attached' => ['library' => []]],
      ],
      [
        ['#attached' => ['library' => []]],
        TRUE,
        TRUE,
        FALSE,
        ['#attached' => ['library' => ['htmx/drupal']]],
      ],
    ];
  }

}
