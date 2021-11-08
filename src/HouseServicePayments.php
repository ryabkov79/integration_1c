<?php

namespace Drupal\integration_1c;

use Drupal\reports\PaymentsBills;
use Drupal\Core\Logger\LoggerChannelInterface;

/**
 * Class PaymentsBills.
 */
class HouseServicePayments {

  /**
   * Payments & Bill report service.
   *
   * @var \Drupal\reports\PaymentsBills
   */
  protected $paymentsBills;

  /**
   * Drupal\Core\Logger\LoggerChannelInterface definition.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $logger;

  /**
   * Constructs a new PaymentsBills object.
   *
   * @param \Drupal\reports\PaymentsBills $paymentsBills
   *   Database connection.
   * @param \Drupal\Core\Logger\LoggerChannelInterface $logger
   *   Logger channel.
   */
  public function __construct(PaymentsBills $paymentsBills, LoggerChannelInterface $logger) {
    $this->paymentsBills = $paymentsBills;
    $this->logger = $logger;
  }

  /**
   * Returns house payments.
   *
   * @param int $page
   *   Page.
   * @param null|int $houseId
   *   House id.
   * @param null|string $date
   *   Date.
   *
   * @return array
   *   Payments array.
   *
   * @throws \Exception
   */
  public function getHouseServicePayments($date = NULL, $houseId = NULL):array {
    $query = $this->paymentsBills->paymentsServiceQuery($date);
    if ($houseId) {
      $query->condition('house_id.field_id_value', $houseId);
    }
    $query->addExpression("DATE_FORMAT(date.field_date_value, '%Y-%m-01')", 'date');
    $query->groupBy('date');
    return $query->execute()->fetchAll(\PDO::FETCH_ASSOC);
  }

}
