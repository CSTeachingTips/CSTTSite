<?php

/**
 * @file
 * Contains \Drupal\ds\Plugin\Derivative\DynamicField.
 */

namespace Drupal\ds\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DerivativeBase;

/**
 * Retrieves dynamic field plugin definitions.
 */
abstract class DynamicField extends DerivativeBase {

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions(array $base_plugin_definition) {

    $custom_fields = config_get_storage_names_with_prefix('ds.field.');

    foreach ($custom_fields as $config) {
      $field = \Drupal::config($config)->get();
      if ($field['type'] == $this->getType()) {
        foreach ($field['entities'] as $entity_type) {
          $key = $entity_type . '-' . $field['id'];
          $this->derivatives[$key] = $base_plugin_definition;
          $this->derivatives[$key] += array(
            'title' => $field['label'],
            'properties' => $field['properties'],
            'entity_type' => $entity_type,
          );
          if (!empty($field['ui_limit'])) {
            $this->derivatives[$key]['ui_limit'] = explode("\n", $field['ui_limit']);
            // Ensure that all strings are trimmed, eg. don't have extra spaces,
            // \r chars etc.
            foreach ($this->derivatives[$key]['ui_limit'] as $k => $v) {
              $this->derivatives[$key]['ui_limit'][$k] = trim($v);
            }
          }
        }
      }
    }

    return $this->derivatives;
  }

  /**
   * {@inheritdoc}
   */
  protected function getType() {
    return '';
  }
}
