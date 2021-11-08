<?php

namespace Drupal\integration_1c;

/**
 * Directory Class.
 */
class Directory {

  /**
   * Data structure.
   *
   * @var array
   */
  protected $dataStructure = [
    'for_all' => [
      'to_form_id' => [
        'date', 'house_id',
      ],
    ],
    'sections' => [
      'Transactions' => [
        'to_form_id' => [
          'partner_id', 'service_id',
        ],
        'node_type' => 'firm_payments_bills',
        'fields' => [
          'field_text_2' => 'partner_title',
          'field_date' => 'date',
          'field_balance_end' => [
            'name' => 'balance_end',
            'mutable' => TRUE,
          ],
          'field_balance_start' => [
            'name' => 'balance_start',
            'mutable' => TRUE,
          ],
          'field_accrued' => [
            'name' => 'accrued',
            'mutable' => TRUE,
          ],
          'field_paid' => [
            'name' => 'paid',
            'mutable' => TRUE,
          ],
          'field_text_1' => 'service_title',
          'field_text_3' => [
            'name' => 'service_group_title',
            'mutable' => TRUE,
          ],
        ],
      ],
      'HouseBalance' => [
        'to_form_id' => [],
        'node_type' => 'house_balance',
        'fields' => [
          'field_date' => 'date',
          'field_balance_end' => [
            'name' => 'balance',
            'mutable' => TRUE,
          ],
        ],
      ],
      'OtherIncome' => [
        'to_form_id' => [
          'partner_inn', 'service_id',
        ],
        'node_type' => 'other_income',
        'fields' => [
          'field_text_2' => 'partner_title',
          'field_date' => 'date',
          'field_balance_end' => [
            'name' => 'balance_end',
            'mutable' => TRUE,
          ],
          'field_balance_start' => [
            'name' => 'balance_start',
            'mutable' => TRUE,
          ],
          'field_accrued' => [
            'name' => 'accrued',
            'mutable' => TRUE,
          ],
          'field_paid' => [
            'name' => 'paid',
            'mutable' => TRUE,
          ],
          'field_text_1' => 'service_title',
        ],
      ],
    ],
  ];

  /**
   * Get data structure.
   *
   * @return array
   *   DataStructure array.
   */
  public function getDataStructure(): array {
    return $this->dataStructure;
  }

}
