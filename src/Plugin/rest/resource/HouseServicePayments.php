<?php

namespace Drupal\integration_1c\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Cache\CacheableMetadata;

/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "houses_payments_by_services_rest_resource",
 *   label = @Translation("Houses payments by services and months."),
 *   uri_paths = {
 *     "canonical" = "/v1/houses-payments-by-services"
 *   }
 * )
 */
class HouseServicePayments extends ResourceBase {

  /**
   * A current user instance.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Drupal\accounting_sync\PaymentsService definition.
   *
   * @var \Drupal\reports\PaymentsBills
   */
  protected $service;

  /**
   * Symfony\Component\HttpFoundation\Request definition.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $pluginId, $pluginDefinition) {
    $instance = parent::create($container, $configuration, $pluginId, $pluginDefinition);
    $instance->logger = $container->get('logger.factory')->get('accounting_sync');
    $instance->currentUser = $container->get('current_user');
    $instance->service = $container->get('integration_1c.house_service_payments');
    $instance->request = $container->get('request_stack')->getCurrentRequest();
    return $instance;
  }

  /**
   * Responds to GET requests.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The HTTP response object.
   *
   * @throws \Exception
   */
  public function get():ResourceResponse {
    $query = $this->request->query->all();
    $houseId = NestedArray::getValue($query, ['house_id']);
    $date = NestedArray::getValue($query, ['date']);
    $data = $this->service->getHouseServicePayments($date, $houseId);
    $response = new ResourceResponse($data, 200);
    $cacheDependency = CacheableMetadata::createFromRenderArray(
      [
        '#cache' => [
          'max-age' => 3600,
          'contexts' => ['url.query_args'],
        ],
      ]
    );
    $response->addCacheableDependency($cacheDependency);
    return $response;

  }

}
