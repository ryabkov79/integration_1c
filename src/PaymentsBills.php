<?php

namespace Drupal\integration_1c;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\node\NodeInterface;

/**
 * Class PaymentsBills.
 */
class PaymentsBills {

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Drupal\Core\Entity\EntityStorageInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $nodeStorage;

  /**
   * Drupal\Core\Logger\LoggerChannelInterface definition.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $logger;

  /**
   * Data structure.
   *
   * @var array
   */
  protected $dataStructure;

  /**
   * Constructs a new PaymentsBills object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   Entity Type Manager service.
   * @param \Drupal\Core\Logger\LoggerChannelInterface $logger
   *   Logger channel.
   * @param Directory $directory
   *   Directory.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, LoggerChannelInterface $logger, Directory $directory) {
    $this->logger = $logger;
    $this->entityTypeManager = $entityTypeManager;
    $this->nodeStorage = $entityTypeManager->getStorage('node');
    $this->dataStructure = $directory->getDataStructure();
  }

  /**
   * Imports firm payments and Bills.
   *
   * @param array $data
   *   Data to import.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function import(array $data) {
    foreach ($this->dataStructure['sections'] as $nameSection => $propertySection) {
      if (isset($data[$nameSection])) {
        foreach ($data[$nameSection] as $item) {
          $item['id'] = $this->getItemId($propertySection, $item);
          $nid = $this->getNidById($propertySection['node_type'], $item['id']);
          if ($nid) {
            $node = $this->nodeStorage->load($nid);
            $this->updateNode($item, $node, $propertySection['fields']);
          }
          else {
            $this->createNode($propertySection['node_type'], $item, $propertySection['fields']);
          }
        }
      }
    }
  }

  /**
   * Creates node by given transaction data.
   *
   * @param string $type
   *   Node type.
   * @param array $item
   *   Transaction data.
   * @param array $fields
   *   Fields.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function createNode(string $type, array $item, array $fields) {
    $houseNid = $this->getNidById('house', $item['house_id']);
    if (!$houseNid) {
      $this->logger->warning('Unable to create node house @house not found', [
        '@house' => $item['house_id'],
      ]);
      return;
    }
    $values = [
      'type' => $type,
      'title' => $item['id'],
      'field_id' => $item['id'],
      'field_house_single' => $houseNid,
    ];
    foreach ($fields as $key => $field) {
      $values[$key] = $item[is_array($field) ? $field['name'] : $field];
    }
    $node = $this->nodeStorage->create($values);
    $node->save();
  }

  /**
   * Updates node by given transaction data, if needed.
   *
   * @param array $item
   *   Transaction data.
   * @param \Drupal\node\NodeInterface $node
   *   Node to update.
   * @param array $fields
   *   Fields.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function updateNode(array $item, NodeInterface $node, array $fields) {
    $changed = FALSE;
    foreach ($fields as $key => $field) {
      if (is_array($field)
        && isset($field['mutable'])
        && $field['mutable']
        && $node->get($key)->getString() != $item[$field['name']]
      ) {
        $node->set($key, $item[$field['name']]);
        $changed = TRUE;
      }
    }
    if ($changed) {
      $node->save();
    }
  }

  /**
   * Returns Item Id.
   *
   * @param array $propertySection
   *   Name section.
   * @param array $item
   *   Item section.
   *
   * @return string
   *   Item ID.
   */
  protected function getItemId(array $propertySection, array $item) {
    $fields = array_merge(
      $this->dataStructure['for_all']['to_form_id'],
      $propertySection['to_form_id']
    );
    foreach ($fields as $field) {
      $values[] = $item[$field];
    }
    return implode('_', $values ?? []);
  }

  /**
   * GetNidById.
   *
   * @param string $type
   *   Node type.
   * @param string $fieldId
   *   Field ID value.
   *
   * @return false|int
   *   Nid or FALSE if not found.
   */
  protected function getNidById(string $type, string $fieldId) {
    $nodes = $this->nodeStorage->loadByProperties(
      ['type' => $type, 'field_id' => $fieldId]
    );
    if (!empty($nodes)) {
      $node = reset($nodes);
      return $node->id();
    }
    return FALSE;
  }

}
