<?php

namespace Drupal\integration_1c\Plugin\rest\resource;

use Drupal\rest\ModifiedResourceResponse;
use Drupal\rest\Plugin\ResourceBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "firm_payments_bills",
 *   label = @Translation("Firm Payments and Bills import"),
 *   uri_paths = {
 *     "create" = "/v1/firm-payments-bills"
 *   }
 * )
 */
class PaymentsBills extends ResourceBase {
  /**
   * A current user instance.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Service implemented mail import logic.
   *
   * @var \Drupal\integration_1c\PaymentsBills
   */
  protected $service;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $pluginId, $pluginDefinition) {
    $instance = parent::create($container, $configuration, $pluginId, $pluginDefinition);
    $instance->logger = $container->get('logger.factory')->get('integration_1c');
    $instance->currentUser = $container->get('current_user');
    $instance->service = $container->get('integration_1c.payments_bills');

    return $instance;
  }

  /**
   * Responds to POST requests.
   *
   * @param array $payload
   *   Request content.
   *
   * @return \Drupal\rest\ModifiedResourceResponse
   *   The HTTP response object
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   * @throws \Drupal\Core\Entity\EntityStorageException
   *   Throws exception expected.
   */
  public function post(array $payload) {
    $this->service->import($payload);
    return new ModifiedResourceResponse($payload, 200);
  }

}
