<?php

/**
 * @file
 * Contains \Drupal\ds\Plugin\DsField\Date.
 */

namespace Drupal\ds\Plugin\DsField;

/**
 * The base plugin to create DS post date plugins.
 */
abstract class Date extends DsFieldBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $field = $this->getFieldConfiguration();
    $date_format = str_replace('ds_post_date_', '', $field['formatter']);
    $render_key = $this->getRenderKey();

    return array(
      '#markup' => format_date($this->entity()->{$render_key}->value, $date_format),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function formatters() {
    $date_types = \Drupal::entityManager()
      ->getStorageController('date_format')
      ->loadMultiple();

    $date_formatters = array();
    foreach ($date_types as $machine_name => $value) {
      if ($value->isLocked()) {
        continue;
      }
      $date_formatters['ds_post_date_' . $machine_name] = t($value->id());
    }

    return $date_formatters;
  }

  /**
   * Returns the entity render key for this field.
   */
  public function getRenderKey() {
    return '';
  }

}
